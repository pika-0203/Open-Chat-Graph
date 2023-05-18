<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Statistics\OpenChatStatisticsSearch;

class SearchPageController
{
    function index(OpenChatStatisticsSearch $openChatStatsSearch, string $q, int $p)
    {
        // キーワードが空の場合
        if ($q === '') {
            return redirect(responseCode: 301);
        }

        $list = $openChatStatsSearch->get($q, $p === 0 ? 1 : $p);
        if ($list === false) {
            // ページ番号が最大数を超えている場合
            return false;
        } elseif ($list === null) {
            // 検索結果が0件の場合
            http_response_code(404);
        }

        $name = "「{$q}」の検索結果";

        $_meta = meta()->setTitle($name);
        $_css = ['room_list_12', 'site_header_10', 'site_footer_6', 'search_form_3'];

        return view('statistics/search_content', compact('_meta', '_css', 'q') + ($list ?? []));
    }
}
