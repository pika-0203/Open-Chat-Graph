<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Statistics\OpenChatStatisticsRanking;
use App\Config\AppConfig;

class IndexPageController
{
    function index(OpenChatStatisticsRanking $openChatStatsRanking)
    {
        $rankingList = getArrayFromFile(AppConfig::FILEPATH_TOP_RANKINGLIST);

        if (session()->has('id')) {
            // セッションにIDのリクエストがある場合
            $openChatRepository = app()->make(\App\Models\Repositories\OpenChatRepositoryInterface::class);
            $rankingList['requestOpenChat'] = $openChatRepository->getOpenChatById(session('id'));
        }

        $_css = ['room_list_14', 'site_header_13', 'site_footer_7'];
        $_meta = meta()->isTopPage();
        $_meta->title = $_meta->title . ' | ' . 'メンバー数の統計をグラフで分析';

        return view('statistics/top_content', compact('_meta', '_css') + $rankingList);
    }
}
