<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Services\User\MyOpenChatList;
use App\Services\StaticData\StaticDataGeneration;

class IndexPageController
{
    function index(StaticDataGeneration $staticDataGeneration)
    {
        $rankingList = $staticDataGeneration->getTopPageData();

        // セッションにIDのリクエストがある場合
        if (session()->has('id')) {
            /** 
             * @var OpenChatPageRepositoryInterface $openChatRepository
             */
            $openChatRepository = app(OpenChatPageRepositoryInterface::class);
            $rankingList['requestOpenChat'] = $openChatRepository->getOpenChatById(session('id'));
        }

        $myList = [];
        
        // クッキーにピン留めがある場合
        if (cookie()->has('myList')) {
            /** 
             * @var MyOpenChatList $myOpenChatList
             */
            $myOpenChatList = app(MyOpenChatList::class);
            $myList = $myOpenChatList->init() ? $myOpenChatList->get() : [];
        }

        $_css = ['room_list', 'site_header', 'site_footer'];
        $_meta = meta();
        $_meta->title = "{$_meta->title} | オープンチャットの人数統計とグラフ分析";

        return view('top_content', compact('_meta', '_css', 'myList') + $rankingList);
    }
}
