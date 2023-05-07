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
            return false;
        }

        $_css = ['room_list', 'site_header', 'site_footer'];
        $_meta = meta();

        $name = '急上昇ランキング';

        $_meta = meta()->setTitle($name);

        return view('statistics/header', compact('_meta', '_css'))
            ->make('statistics/ranking_content', $rankingList)
            ->make('statistics/footer');
    }
}
