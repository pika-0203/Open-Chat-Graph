<?php

use App\Models\Repositories\OpenChatListRepositoryInterface;
use App\Config\AppConfig;

class IndexPageController
{
    function index(OpenChatListRepositoryInterface $openChatListRepository)
    {
        $openChatList = $openChatListRepository->findMemberStatsRanking(0, AppConfig::OPEN_CHAT_RANKING_LIMIT);

        $requestOpenChat = null;
        if (session()->has('id')) {
            $openChatRepository = app()->make(\App\Models\Repositories\OpenChatRepositoryInterface::class);
            $requestOpenChat = $openChatRepository->getOpenChatById(session('id'));
        }

        $_css = ['room_list', 'site_header'];
        $_meta = meta();

        return view('statistics/header', compact('_meta', '_css'))
            ->make('statistics/top_content', compact('openChatList', 'requestOpenChat'))
            ->make('statistics/footer');
    }
}
