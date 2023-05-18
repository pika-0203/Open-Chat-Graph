<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Statistics\OpenChatStatisticsRanking;

class IndexPageController
{
    function index(OpenChatStatisticsRanking $openChatStatsRanking, ?int $pageNumber)
    {
        $rankingList = $openChatStatsRanking->get($pageNumber ?? 1);

        if (session()->has('id')) {
            // セッションにIDのリクエストがある場合
            $openChatRepository = app()->make(\App\Models\Repositories\OpenChatRepositoryInterface::class);
            $rankingList['requestOpenChat'] = $openChatRepository->getOpenChatById(session('id'));
        }

        $_css = ['room_list_12', 'site_header_10', 'site_footer_6'];
        $_meta = meta()->isTopPage();

        return view('statistics/top_content', compact('_meta', '_css') + $rankingList);
    }
}
