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
        int $page
    ) {
        $titleValue = [
            'c' => !!$change ? 'c' : false,
            'p' => !!$publish ? 'p' : false,
            'per' => $percent > 50 ? $percent : false
        ];
        $_meta = meta()
            ->setTitle('オプチャ公式ランキング掲載の分析' . ($page > 1 ? "({$page}ページ目)" : '') . implode(',', array_filter($titleValue)))
            ->setDescription(
                'オプチャ公式ランキングへの掲載・未掲載の状況を一覧表示します。ルーム内容の変更後などに起こる掲載状況（検索落ちなど）の変動を捉えることができます。'
            );

        $_css = ['room_list', 'site_header', 'site_footer'];

        $_updatedAt = new \DateTime(file_get_contents(AppConfig::HOURLY_REAL_UPDATED_AT_DATETIME));
        $_now = file_get_contents(AppConfig::HOURLY_CRON_UPDATED_AT_DATETIME);

        $rankingBanData = $rakingBanPageService->getAllOrderByDateTime(
            !!$change,
            !!$publish,
            $percent,
            $page
        );

        if (!$rankingBanData && $page > 1) return false;
        if (!$rankingBanData && $page === 1) {
            return view(
                'ranking_ban_content',
                compact(
                    '_meta',
                    '_css',
                    '_updatedAt',
                    '_now',
                )
            );
        }

        $path = 'labs/publication-analytics';
        $params = compact('change', 'publish', 'percent');

        [$title, $_select, $_label] = $rankingBanSelectElementPagination->geneSelectElementPagerAsc(
            $path,
            $params,
            $page,
            $rankingBanData['totalRecords'],
            AppConfig::OPEN_CHAT_LIST_LIMIT,
            $rankingBanData['maxPageNumber'],
            array_reverse($rankingBanData['labelArray'])
        );

        $openChatList =  $rankingBanData['openChatList'];
        $_pagerNavArg = [
            'path' => $path,
            'params' => $params,
            'pageNumber' => $page,
            'maxPageNumber' => $rankingBanData['maxPageNumber']
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
            )
        );
    }
}
