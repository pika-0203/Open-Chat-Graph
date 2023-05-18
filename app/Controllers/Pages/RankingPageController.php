<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Statistics\OpenChatStatisticsRanking;

class RankingPageController
{
    function index(OpenChatStatisticsRanking $openChatStatsRanking, ?int $pageNumber)
    {
        $rankingList = $openChatStatsRanking->get($pageNumber ?? 1);
        if (!$rankingList) {
            // 最大ページ数を超えてる場合は404
            return false;
        }
        
        $name = '急上昇ランキング';
        
        $_meta = meta()->setTitle($name);
        $_css = ['room_list_12', 'site_header_10', 'site_footer_6'];

        return view('statistics/ranking_content', compact('_meta', '_css') + $rankingList);
    }
}
