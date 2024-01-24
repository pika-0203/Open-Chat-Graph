<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Statistics\OpenChatStatisticsRecent;
use App\Config\AppConfig;
use App\Views\SelectElementPagination;
use App\Views\Schema\PageBreadcrumbsListSchema;

class RecentPageController
{
    function __construct(
        private OpenChatStatisticsRecent $openChatStatsRecent,
        private SelectElementPagination $pagination
    ) {
    }

    function index(?int $pageNumber)
    {
        $pageNumber = $pageNumber ?? 1;
        $rankingList = $this->openChatStatsRecent->getAllOrderByRegistrationDate($pageNumber, AppConfig::OPEN_CHAT_LIST_LIMIT);

        if (!$rankingList) {
            // 最大ページ数を超えてる場合は404
            return false;
        }

        $path = 'recent';
        $title = '最近登録されたオープンチャット';

        trimOpenChatListDescriptions($rankingList['openChatList']);
        return $this->generateView($path, $title, $pageNumber, $rankingList);
    }

    function changes(?int $pageNumber)
    {
        $pageNumber = $pageNumber ?? 1;
        $rankingList = $this->openChatStatsRecent->getRecentChanges($pageNumber, AppConfig::OPEN_CHAT_LIST_LIMIT);

        if (!$rankingList) {
            // 最大ページ数を超えてる場合は404
            return false;
        }

        $path = 'recent/changes';
        $title = '最近アーカイブされたオープンチャット';

        trimOpenChatListDescriptions($rankingList['openChatList']);
        return $this->generateView($path, $title, $pageNumber, $rankingList);
    }

    function deleted(?int $pageNumber)
    {
        $pageNumber = $pageNumber ?? 1;
        $rankingList = $this->openChatStatsRecent->getRecentDeleted($pageNumber, AppConfig::OPEN_CHAT_LIST_LIMIT);

        if (!$rankingList) {
            // 最大ページ数を超えてる場合は404
            return false;
        }

        $path = 'recent/deleted';
        $title = '最近削除されたオープンチャット';

        trimOpenChatListDescriptions($rankingList['openChatList']);
        return $this->generateView($path, $title, $pageNumber, $rankingList);
    }

    private function generateView(string $path, string $pageTitle, int $pageNumber, array $rankingList)
    {
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

        // メタタグ、構造化データ
        $subTitle = $pageNumber === 1 ? '' : "({$pageNumber}ページ目)";
        $_meta = meta()->setTitle($pageTitle . $subTitle);
        $_css = ['room_list', 'site_header', 'site_footer'];
        $_schema = $pageNumber === 1 ? (new PageBreadcrumbsListSchema)->generateSchema($pageTitle, $path) : '';

        trimOpenChatListDescriptions($rankingList);

        return view(
            'recent_content',
            compact('_meta', '_css', '_select', '_label', '_schema', 'path') + $rankingList
        );
    }
}
