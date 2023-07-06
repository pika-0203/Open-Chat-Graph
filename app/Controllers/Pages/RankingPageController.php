<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use Shadow\Kernel\Reception;
use App\Services\Statistics\OpenChatStatisticsRanking;
use App\Config\AppConfig;
use App\Views\SelectElementPagination;

class RankingPageController
{
    function index(
        OpenChatStatisticsRanking $openChatStatsRanking,
        SelectElementPagination $pagination,
        Reception $reception,
    ) {
        $rankingInfo = getArrayFromFile(AppConfig::FILEPATH_TOP_RANKINGLIST);
        $pageNumber = $reception->input('pageNumber') ?? 1;
        $disabledBtns = [];

        // オプチャリストをリポジトリから取得
        if ($reception->input('l') === 'w') {
            $rankingList = $openChatStatsRanking->getPastWeekRanking($pageNumber, AppConfig::OPEN_CHAT_LIST_LIMIT);
            $disabledBtns['weekly'] = true;
        } elseif ($reception->input('l') === 'm') {
            $rankingList = $openChatStatsRanking->getMemberRanking($pageNumber, AppConfig::OPEN_CHAT_LIST_LIMIT);
            $disabledBtns['member'] = true;
        } else {
            $rankingList = $openChatStatsRanking->getDailyRanking($pageNumber, AppConfig::OPEN_CHAT_LIST_LIMIT);
            $disabledBtns['daily'] = true;
        }

        if (!$rankingList) {
            // 最大ページ数を超えてる場合は404
            return false;
        }

        // ボタンのdisabledを出力するクロージャー
        $_isDisabledBtn = function ($name) use ($disabledBtns) {
            if (isset($disabledBtns[$name])) echo 'disabled';
        };

        // クエリストリング、週次・日次フラグ
        $_queryString = '';
        $isDaily = true;
        if ($reception->input('l') === 'w') {
            $_queryString .= '?l=w';
            $isDaily = false;
        } elseif ($reception->input('l') === 'm') {
            $_queryString .= '?l=m';
        }

        // ページネーションのselect要素
        [$title, $_select, $_label] = $pagination->geneSelectElementPagerAsc(
            'ranking',
            $_queryString,
            $pageNumber,
            $rankingList['totalRecords'],
            AppConfig::OPEN_CHAT_LIST_LIMIT,
            $rankingList['maxPageNumber']
        );

        // メタタグ、構造化データ
        $subTitle = $pageNumber === 1 ? '' : "({$pageNumber}ページ目)";
        $_meta = meta()->setTitle('【毎日更新】参加人数のランキング' . $subTitle);
        $_css = ['room_list_26', 'site_header_21', 'site_footer_18'];
        $_schema = $pageNumber === 1 ? (new \App\Views\Schema\OcPageBreadcrumbsListSchema)->generateSchema() : '';

        return view(
            'statistics/ranking_content',
            compact('_meta', '_css', '_select', '_label', '_schema', '_isDisabledBtn', '_queryString', 'isDaily') + $rankingList + ['updatedAt' => $rankingInfo['updatedAt']]
        );
    }
}
