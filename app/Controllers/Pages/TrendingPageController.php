<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Statistics\OpenChatStatisticsRanking;
use App\Config\AppConfig;
use App\Views\SelectElementPagination;
use App\Views\Schema\OcPageBreadcrumbsListSchema;

class TrendingPageController
{
    function index(
        OpenChatStatisticsRanking $openChatStatsRanking,
        SelectElementPagination $pagination,
        OcPageBreadcrumbsListSchema $schema,
        ?int $pageNumber
    ) {
        $rankingInfo = getArrayFromFile(AppConfig::FILEPATH_TOP_RANKINGLIST);

        $pageNumber = $pageNumber ?? 1;
        $rankingList = $openChatStatsRanking->getDailyRanking($pageNumber, AppConfig::OPEN_CHAT_LIST_LIMIT);
        if (!$rankingList) {
            // 最大ページ数を超えてる場合は404
            return false;
        }

        [$title, $_select, $_label] = $pagination->geneSelectElementPagerAsc(
            'trending',
            $pageNumber,
            $rankingList['totalRecords'],
            AppConfig::OPEN_CHAT_LIST_LIMIT,
            $rankingList['maxPageNumber']
        );

        $subTitle = $pageNumber === 1 ? '' : "({$pageNumber}ページ目)";
        $_meta = meta()->setTitle('【毎日更新】参加人数の急上昇ランキング' . $subTitle);
        $_css = ['room_list_21', 'site_header_17', 'site_footer_15'];
        $_schema = $schema->generateSchema($pageNumber);

        return view(
            'statistics/trending_content',
            compact('_meta', '_css', '_select', '_label', '_schema') + $rankingList + ['updatedAt' => $rankingInfo['updatedAt']]
        );
    }
}
