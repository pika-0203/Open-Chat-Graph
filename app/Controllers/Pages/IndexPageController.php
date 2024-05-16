<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\CommentRepositories\RecentCommentListRepositoryInterface;
use App\Services\Recommend\OfficialPageList;
use App\Services\StaticData\StaticDataFile;
use App\Views\Schema\PageBreadcrumbsListSchema;
use DateTime;

class IndexPageController
{
    function index(
        StaticDataFile $staticDataGeneration,
        RecentCommentListRepositoryInterface $recentCommentListRepository,
        PageBreadcrumbsListSchema $pageBreadcrumbsListSchema,
        OfficialPageList $officialPageList,
    ) {
        $dto = $staticDataGeneration->getTopPageData();

        $_css = ['room_list', 'site_header', 'site_footer', 'search_form', 'recommend_list'];
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


        $dto->recentCommentList = $recentCommentListRepository->findRecentCommentOpenChatAll(0, 15);
        $updatedAtHouryCron = $dto->rankingUpdatedAt;

        if (isset($dto->recentCommentList[0]['time'])) {
            $updatedAtComments = new \DateTime($dto->recentCommentList[0]['time']);
            $_updatedAt = $updatedAtHouryCron > $updatedAtComments ? $updatedAtHouryCron : $updatedAtComments;

            $updatedAtComments->modify('+ 2hour');
            $newComment = new \DateTime() < $updatedAtComments;
        } else {
            $_updatedAt = $updatedAtHouryCron;
            $newComment = false;
        }

        $dto->hourlyList = array_slice($dto->hourlyList, 0, 5);
        $dto->dailyList = array_slice($dto->dailyList, 0, 5);
        $dto->weeklyList = array_slice($dto->weeklyList, 0, 5);
        $dto->popularList = array_slice($dto->popularList, 0, 5);

        $tags = $dto->recommendList ?? [];

        $officialDto = $officialPageList->getListDto('1', 'スペシャルオープンチャット')[0];
        $officialDto2 = $officialPageList->getListDto('2', '公式認証オープンチャット')[0];

        cacheControl(180);

        return view('top_content', compact(
            '_meta',
            '_css',
            '_schema',
            '_updatedAt',
            'dto',
            'tags',
            'officialDto',
            'officialDto2',
            'newComment',
        ));
    }
}
