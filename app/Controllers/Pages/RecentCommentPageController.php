<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Services\Admin\AdminAuthService;
use App\Services\StaticData\StaticDataFile;
use App\Views\RecentComment;
use App\Views\RecentCommentSelectElementPagination;
use App\Views\Schema\PageBreadcrumbsListSchema;
use Shadow\Kernel\Reception;

class RecentCommentPageController
{
    function __construct(
        private RecentComment $recentComment,
        private RecentCommentSelectElementPagination $pagination,
        private PageBreadcrumbsListSchema $breadcrumbsShema,

    ) {}

    function index(
        AdminAuthService $adminAuthService,
        StaticDataFile $staticDataGeneration,
    ) {
        $recentPage = Reception::input('page');
        $rankingList = $this->recentComment->getAllOrderByRegistrationDate(
            $recentPage,
            AppConfig::RECENT_COMMENT_LIST_LIMIT
        );

        if (!$rankingList) {
            // 最大ページ数を超えてる場合は404
            return false;
        }

        $path = 'comments-timeline';
        $pageTitle = 'コメントのタイムライン';
        $_css = ['room_list', 'site_header', 'site_footer'];

        $isAdmin = $adminAuthService->auth();

        // ページネーションのselect要素
        [$title, $_select, $_label] = $this->pagination->geneSelectElementPagerAsc(
            $path,
            '',
            $rankingList['pageNumber'],
            $rankingList['totalRecords'],
            AppConfig::RECENT_COMMENT_LIST_LIMIT,
            $rankingList['maxPageNumber'],
            $rankingList['labelArray']
        );

        $subTitle = $recentPage === 0 ? '' : "({$recentPage}ページ目)";
        $_meta = meta()->setTitle($pageTitle . $subTitle);

        $_breadcrumbsShema = $this->breadcrumbsShema->generateSchema(
            'コメント',
            'comments-timeline',
            $subTitle,
            $subTitle ? ((string)$recentPage) : ''
        );

        $topPageDto = $staticDataGeneration->getTopPageData();

        return view(
            'recent_comment',
            compact(
                '_meta',
                '_css',
                '_select',
                '_label',
                'path',
                'isAdmin',
                '_breadcrumbsShema',
                'topPageDto'
            ) + $rankingList
        );
    }
}
