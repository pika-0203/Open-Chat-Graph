<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Statistics\OpenChatStatisticsSearch;

//class SearchPageController
{
    public function index(OpenChatStatisticsSearch $openChatStatsSearch, string $q, int $p)
    {
        // キーワードが空の場合
        if (trim($q) === '') {
            return redirect(responseCode: 301);
        }

        $list = $openChatStatsSearch->get($q, $p === 0 ? 1 : $p);
        if ($list === false) {
            // ページ番号が最大数を超えている場合
            return false;
        }

        $_meta = meta()->setTitle("「{$q}」の検索結果");
        $_css = ['room_list', 'site_header', 'site_footer', 'search_form'];

        if ($list === []) {
            // 検索結果が0件の場合
            http_response_code(404);
        }

        return view('search_content', compact('_meta', '_css', 'q') + $list);
    }
}
