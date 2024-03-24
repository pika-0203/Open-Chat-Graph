<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\CommentRepositories\RecentCommentListRepositoryInterface;
use App\Services\User\MyOpenChatList;
use App\Services\StaticData\StaticDataFile;
use App\Views\Schema\PageBreadcrumbsListSchema;
use DateTime;

class IndexPageController
{
    function index(
        StaticDataFile $staticDataGeneration,
        RecentCommentListRepositoryInterface $recentCommentListRepository,
        PageBreadcrumbsListSchema $pageBreadcrumbsListSchema
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
        $_meta->title = "{$_meta->title} | オープンチャットの統計情報とコメント機能";

        $_schema = $pageBreadcrumbsListSchema->generateStructuredDataWebSite(
            'オプチャグラフ',
            $_meta->description,
            url(),
            url('assets/ogp.png'),
            'pika-0203',
            'https://github.com/pika-0203',
            'https://avatars.githubusercontent.com/u/132340402?v=4',
            'オプチャグラフ',
            url('assets/icon-192x192.png'),
            new DateTime('2023-05-06 00:00:00'),
            $dto->hourlyUpdatedAt
        );

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
            '_schema'
        ));
    }
}
