<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Services\RankingBan\RakingBanPageService;
use App\Views\RankingBanSelectElementPagination;

class RankingBanLabsPageController
{
    function index(
        RakingBanPageService $rakingBanPageService,
        RankingBanSelectElementPagination $rankingBanSelectElementPagination,
        int $change,
        int $publish,
        int $percent,
        int $page,
        string $keyword
    ) {
        $titleValue = implode(', ', array_filter([
            'p' => $publish === 1 ? '現在未掲載' : ($publish === 0 ? '再掲載済み' : '全て'),
            'c' => $change === 1 ? 'ルーム内容変更なし' : ($change === 0 ? 'ルーム内容変更あり' : '全て'),
            'per' => $percent < 100 ? "ランク上位{$percent}%" : '全て',
            'keyword' => $keyword !== '' ? "\n「{$keyword}」" : false,
        ]));

        $_meta = meta()
            ->setTitle('オプチャ公式ランキング掲載の分析 ' . ($page > 1 ? "({$page}ページ目) " : '') . $titleValue)
            ->setDescription(
                'オプチャ公式ランキングへの掲載・未掲載の状況を一覧表示します。ルーム内容の変更後などに起こる掲載状況（検索落ちなど）の変動を捉えることができます。'
            );

        $_css = ['room_list', 'site_header', 'site_footer'];

        $_updatedAt = new \DateTime(file_get_contents(AppConfig::HOURLY_REAL_UPDATED_AT_DATETIME));
        $_now = file_get_contents(AppConfig::HOURLY_CRON_UPDATED_AT_DATETIME);

        $rankingBanData = $rakingBanPageService->getAllOrderByDateTime(
            $change,
            $publish,
            $percent,
            $keyword,
            $page
        );

        if (!$rankingBanData && $page > 1) return false;
        if (!$rankingBanData && $page === 1) {
            $totalRecords = 0;
            $maxPageNumber = 0;
            return view(
                'ranking_ban_content',
                compact(
                    '_meta',
                    '_css',
                    '_updatedAt',
                    '_now',
                    'titleValue',
                    'totalRecords',
                    'maxPageNumber',
                )
            );
        }

        $totalRecords = $rankingBanData['totalRecords'];
        $maxPageNumber = $rankingBanData['maxPageNumber'];
        $path = 'labs/publication-analytics';
        $params = compact('change', 'publish', 'percent', 'keyword');

        [$title, $_select, $_label] = $rankingBanSelectElementPagination->geneSelectElementPagerAsc(
            $path,
            $params,
            $page,
            $totalRecords,
            AppConfig::OPEN_CHAT_LIST_LIMIT,
            $rankingBanData['maxPageNumber'],
            array_reverse($rankingBanData['labelArray'])
        );

        $openChatList =  $rankingBanData['openChatList'];
        $_pagerNavArg = [
            'path' => $path,
            'params' => $params,
            'pageNumber' => $page,
            'maxPageNumber' => $maxPageNumber
        ];

        return view(
            'ranking_ban_content',
            compact(
                '_meta',
                '_css',
                'openChatList',
                '_updatedAt',
                '_now',
                '_select',
                '_label',
                '_pagerNavArg',
                'totalRecords',
                'titleValue',
                'maxPageNumber',
            )
        );
    }
}
