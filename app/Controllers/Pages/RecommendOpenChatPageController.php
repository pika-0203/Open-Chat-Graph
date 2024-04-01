<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Views\OpenChatStatisticsRecent;
use App\Config\AppConfig;
use App\Services\Admin\AdminAuthService;
use App\Views\Schema\PageBreadcrumbsListSchema;

class RecommendOpenChatPageController
{
    function __construct(
        private OpenChatStatisticsRecent $openChatStatsRecent,
        private PageBreadcrumbsListSchema $breadcrumbsShema
    ) {
    }

    function index(AdminAuthService $adminAuthService)
    {
        $rankingList = $this->openChatStatsRecent->getAllOrderByRegistrationDate(1, AppConfig::OPEN_CHAT_LIST_LIMIT);
        if (!$rankingList) {
            // 最大ページ数を超えてる場合は404
            return false;
        }

        $pageTitle = '【最新】「なりきり」関連のおすすめ人気オープンチャット50件';
        $_css = ['room_list', 'site_header', 'site_footer'];

        $_meta = meta()->setTitle($pageTitle);

        $_breadcrumbsShema = $this->breadcrumbsShema->generateSchema('おすすめのオプチャ', 'recommend');

        return view(
            'recommend_content',
            compact('_meta', '_css', '_breadcrumbsShema') + $rankingList
        );
    }
}
