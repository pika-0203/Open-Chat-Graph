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

        $pageNumber = $pageNumber ?? 1;
        $rankingList = $openChatStatsRanking->get($pageNumber, AppConfig::OPEN_CHAT_LIST_LIMIT);
        if (!$rankingList) {
            // 最大ページ数を超えてる場合は404
            return false;
        }

        [$title, $_select, $_label] = $pagination->geneSelectElementPagerAsc(
            $pageNumber,
            $rankingList['totalRecords'],
            AppConfig::OPEN_CHAT_LIST_LIMIT,
            $rankingList['maxPageNumber']
        );

        $subTitle = $pageNumber === 1 ? '' : "({$pageNumber}ページ目)";
        $_meta = meta()->setTitle('【毎日更新】人数急上昇のオープンチャットランキング' . $subTitle);
        $_css = ['room_list_16', 'site_header_14', 'site_footer_9'];

        return view(
            'statistics/ranking_content',
            compact('_meta', '_css', '_select', '_label') + $rankingList + ['updatedAt' => $rankingInfo['updatedAt']]
        );
    }
}
