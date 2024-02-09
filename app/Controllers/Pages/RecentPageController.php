<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Views\OpenChatStatisticsRecent;
use App\Config\AppConfig;
use App\Services\Admin\AdminAuthService;
use App\Views\SelectElementPagination;

class RecentPageController
{
    function __construct(
        private OpenChatStatisticsRecent $openChatStatsRecent,
        private SelectElementPagination $pagination
    ) {
    }

    function index(?int $pageNumber, AdminAuthService $adminAuthService)
    {
        $pageNumber = $pageNumber ?? 1;
        $rankingList = $this->openChatStatsRecent->getAllOrderByRegistrationDate($pageNumber, AppConfig::OPEN_CHAT_LIST_LIMIT);

        if (!$rankingList) {
            // 最大ページ数を超えてる場合は404
            return false;
        }
        
        $path = 'recent';
        $pageTitle = '最近登録されたオープンチャット';
        $_css = ['room_list', 'site_header', 'site_footer'];

        $isAdmin = $adminAuthService->auth();

        trimOpenChatListDescriptions($rankingList['openChatList']);

        // ページネーションのselect要素
        [$title, $_select, $_label] = $this->pagination->geneSelectElementPagerAsc(
            $path,
            '',
            $pageNumber,
            $rankingList['totalRecords'],
            AppConfig::OPEN_CHAT_LIST_LIMIT,
            $rankingList['maxPageNumber'],
            $rankingList['labelArray']
        );

        $subTitle = $pageNumber === 1 ? '' : "({$pageNumber}ページ目)";
        $_meta = meta()->setTitle($pageTitle . $subTitle);

        return view(
            'recent_content',
            compact('_meta', '_css', '_select', '_label', 'path', 'isAdmin') + $rankingList
        );
    }
}
