<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\CommentRepositories\RecentCommentListRepositoryInterface;
use App\Services\User\MyOpenChatList;
use App\Services\StaticData\StaticDataFile;
use App\Views\Content\TopPageNews;
use App\Views\Schema\PageBreadcrumbsListSchema;
use DateTime;

class IndexPageController
{
    function index(
        StaticDataFile $staticDataGeneration,
        RecentCommentListRepositoryInterface $recentCommentListRepository,
        PageBreadcrumbsListSchema $pageBreadcrumbsListSchema,
    ) {
        $dto = $staticDataGeneration->getTopPageData();
        $dto->recentCommentList = $recentCommentListRepository->findRecentCommentOpenChatAll(0, 15);

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
        $_meta->title = "{$_meta->title} | オープンチャットの統計情報";

        $_schema = $pageBreadcrumbsListSchema->generateStructuredDataWebSite(
            'オプチャグラフ',
            $_meta->description,
            url(),
            url('assets/ogp.png'),
            new DateTime('2023-05-06 00:00:00'),
            $dto->hourlyUpdatedAt
        );

        $dto->dailyUpdatedAt->modify('-7day');
        $weeklyStart = $dto->dailyUpdatedAt->format('n月j日');
        $weeklyRange =  "{$weeklyStart} 〜 昨日";

        $hourlyTime = $dto->hourlyUpdatedAt->format(\DateTime::ATOM);
        $hourlyEnd = $dto->hourlyUpdatedAt->format('G:i');
        $dto->hourlyUpdatedAt->modify('-1hour');
        $hourlyStart = $dto->hourlyUpdatedAt->format('G:i');

        $_hourlyRange = $hourlyStart . '〜<time datetime="' . $hourlyTime . '">' . $hourlyEnd . '</time>';

        $tags = $dto->recommendList ?? [];
        $_news = array_reverse(TopPageNews::getTopPageNews());

        return view('top_content', compact(
            'dto',
            '_meta',
            '_css',
            'myList',
            '_hourlyRange',
            'weeklyRange',
            'hourlyEnd',
            '_schema',
            'tags',
            '_news'
        ));
    }
}
