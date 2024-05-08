<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AdminConfig;
use App\Models\CommentRepositories\RecentCommentListRepositoryInterface;
use App\Services\Auth\AuthInterface;
use App\Services\User\MyOpenChatList;
use App\Services\StaticData\StaticDataFile;
use App\Services\User\MyOpenChatListUserLogger;
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
        if(cookie()->has('myList')) {
            $myList = $this->listService();
        }

        $_css = ['room_list', 'site_header', 'site_footer', 'search_form'];
        $_meta = meta();
        $_meta->title = "{$_meta->title}｜オープンチャットの統計情報";

        $_schema = $pageBreadcrumbsListSchema->generateStructuredDataWebSite(
            'オプチャグラフ',
            $_meta->description,
            url(),
            url('assets/ogp.png'),
            new DateTime('2023-05-06 00:00:00'),
            $dto->hourlyUpdatedAt
        );

        $tags = $dto->recommendList ?? [];
        $_news = array_reverse(TopPageNews::getTopPageNews());

        $updatedAtHouryCron = $dto->rankingUpdatedAt;
        if (isset($dto->recentCommentList[0]['time'])) {
            $udatedAtComments = new \DateTime($dto->recentCommentList[0]['time']);
            $_updatedAt = $updatedAtHouryCron > $udatedAtComments ? $updatedAtHouryCron : $udatedAtComments;
        } else {
            $_updatedAt = $updatedAtHouryCron;
        }

        $dto->recentCommentList = $recentCommentListRepository->findRecentCommentOpenChatAll(0, 15);
        $dto->hourlyList = array_slice($dto->hourlyList, 0, 5);

        return view('top_content', compact(
            '_meta',
            '_css',
            '_schema',
            '_updatedAt',
            'dto',
            'myList',
            'tags',
            '_news',
        ));
    }

    private function listService(): array
    {
        /** @var MyOpenChatList $myOpenChatList **/
        $myOpenChatList = app(MyOpenChatList::class);

        /** @var AuthInterface $auth **/
        $auth = app(AuthInterface::class);

        /** @var MyOpenChatListUserLogger $myOpenChatListUserLogger **/
        $myOpenChatListUserLogger = app(MyOpenChatListUserLogger::class);

        [$expires, $myListIdArray, $myList] = $myOpenChatList->init();
        if (!$expires) return [];

        $userId = $auth->loginCookieUserId();
        if ($userId === AdminConfig::ADMIN_API_KEY) return $myList;

        $myOpenChatListUserLogger->userMyListLog(
            $userId,
            $expires,
            $myListIdArray
        );

        return $myList;
    }
}
