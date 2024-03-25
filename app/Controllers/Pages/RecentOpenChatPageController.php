<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Views\OpenChatStatisticsRecent;
use App\Config\AppConfig;
use App\Services\Admin\AdminAuthService;
use App\Views\Schema\PageBreadcrumbsListSchema;
use App\Views\SelectElementPagination;

class RecentOpenChatPageController
{
    function __construct(
        private OpenChatStatisticsRecent $openChatStatsRecent,
        private SelectElementPagination $pagination,
        private PageBreadcrumbsListSchema $breadcrumbsShema
    ) {
    }

    function index(?int $recent_page_number, AdminAuthService $adminAuthService)
    {
        $recent_page_number = $recent_page_number ?: 1;
        $rankingList = $this->openChatStatsRecent->getAllOrderByRegistrationDate($recent_page_number, AppConfig::OPEN_CHAT_LIST_LIMIT);

        if (!$rankingList) {
            // 最大ページ数を超えてる場合は404
            return false;
        }

        $path = '/oc?recent_page_number=';
        $pageTitle = '最近登録されたオープンチャット';
        $_css = ['room_list', 'site_header', 'site_footer'];

        $isAdmin = $adminAuthService->auth();

        // ページネーションのselect要素
        [$title, $_select, $_label] = $this->pagination->geneSelectElementPagerAsc(
            $path,
            '',
            $recent_page_number,
            $rankingList['totalRecords'],
            AppConfig::OPEN_CHAT_LIST_LIMIT,
            $rankingList['maxPageNumber'],
            $rankingList['labelArray']
        );

        $subTitle = $recent_page_number === 1 ? '' : "({$recent_page_number}ページ目)";
        $_meta = meta()->setTitle($pageTitle . $subTitle);

        $_breadcrumbsShema = $this->breadcrumbsShema->generateSchema('最近登録されたオープンチャット', 'oc');

        return view(
            'recent_content',
            compact('_meta', '_css', '_select', '_label', 'path', 'isAdmin', '_breadcrumbsShema') + $rankingList
        );
    }
}
