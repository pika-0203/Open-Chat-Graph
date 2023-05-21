<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Statistics\OpenChatStatisticsRanking;
use App\Config\AppConfig;
use App\Views\SelectElementPagination;

class RankingPageController
{
    function index(
        OpenChatStatisticsRanking $openChatStatsRanking,
        SelectElementPagination $pagination,
        ?int $pageNumber
    ) {
        $rankingInfo = getArrayFromFile(AppConfig::FILEPATH_TOP_RANKINGLIST);

        $rankingList = $openChatStatsRanking->get($pageNumber ?? 1, AppConfig::OPEN_CHAT_LIST_LIMIT);
        if (!$rankingList) {
            // 最大ページ数を超えてる場合は404
            return false;
        }

        [$title, $_select, $_label] = $pagination->geneSelectElementPagerAsc(
            $pageNumber ?? 1,
            $rankingList['totalRecords'],
            AppConfig::OPEN_CHAT_LIST_LIMIT,
            $rankingList['maxPageNumber']
        );

        $_meta = meta()->setTitle("参加人数の急上昇ランキング 毎日更新 {$title}");
        $_css = ['room_list_14', 'site_header_13', 'site_footer_7'];

        return view(
            'statistics/ranking_content',
            compact('_meta', '_css', '_select', '_label') + $rankingList + ['updatedAt' => $rankingInfo['updatedAt']]
        );
    }
}
