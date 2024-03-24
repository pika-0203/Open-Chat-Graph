<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\CommentRepositories\RecentCommentListRepositoryInterface;
use App\Services\User\MyOpenChatList;
use App\Services\StaticData\StaticDataFile;

class IndexPageController
{
    function index(
        StaticDataFile $staticDataGeneration,
        RecentCommentListRepositoryInterface $recentCommentListRepository
    ) {
        $dto = $staticDataGeneration->getTopPageData();
        $dto->recentCommentList = $recentCommentListRepository->findRecentCommentOpenChat(0, 10);

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

        $dto->dailyUpdatedAt->modify('-7day');
        $weeklyStart = $dto->dailyUpdatedAt->format('n月j日');
        $weeklyRange =  "{$weeklyStart} 〜 昨日";

        $hourlyEnd = $dto->hourlyUpdatedAt->format('G:i');
        $dto->hourlyUpdatedAt->modify('-1hour');
        $hourlyStart = $dto->hourlyUpdatedAt->format('G:i');
        $hourlyRange = "{$hourlyStart} 〜 {$hourlyEnd}";

        return view('top_content', compact(
            'dto',
            '_meta',
            '_css',
            'myList',
            'hourlyRange',
            'weeklyRange',
        ));
    }
}
