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

        $_css = ['room_list', 'site_header', 'site_footer'];
        $_meta = meta();

        return view('statistics/header', compact('_meta', '_css'))
            ->make('statistics/top_content', $rankingList)
            ->make('statistics/footer');
    }
}
