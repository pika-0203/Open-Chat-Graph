<?php

namespace App\Config;

use Shadow\Kernel\Route;
use App\Middleware\VerifyCsrfToken;
use App\Middleware\RedirectLineWebBrowser;
use App\Services\Admin\AdminAuthService;
use App\Config\OpenChatCrawlerConfig;
use App\Controllers\Api\OcApiController;
use App\Controllers\Pages\ReactRankingPageController;

Route::middlewareGroup(RedirectLineWebBrowser::class)
    ->path('ranking', [ReactRankingPageController::class, 'ranking'])
    ->match(cache(...))

    ->path('ranking/{category}', [ReactRankingPageController::class, 'ranking'])
    ->match(cache(...));

Route::path('member')
    ->match(cache(...));

Route::path('recent');

Route::path('recent/changes');

Route::path('recent/{pageNumber}')
    ->matchNum('pageNumber', min: 1);

Route::path('recent/changes/{pageNumber}')
    ->matchNum('pageNumber', min: 1);

Route::path('search')
    ->matchStr('q', maxLen: 40, emptyAble: true)
    ->match(fn (string $q) => redirect('ranking/?' . http_build_query(['keyword' => $q, 'list' => 'all']), 301));

Route::path('oc/{open_chat_id}/archive/{group_id}')
    ->matchNum('open_chat_id', min: 1)
    ->matchNum('group_id', min: 1)
    ->match(cache(...));

Route::path('oc/{open_chat_id}')
    ->matchNum('open_chat_id', min: 1);

Route::path('oc/{open_chat_id}/json', [OcApiController::class, 'json'])
    ->matchNum('open_chat_id', min: 1);

Route::path('oc/{open_chat_id}/official_ranking_position', [OcApiController::class, 'officialRankingPosition'])
    ->matchNum('open_chat_id', min: 1);

Route::middlewareGroup(VerifyCsrfToken::class)
    ->path('/')
    ->middleware([RedirectLineWebBrowser::class])

    ->path('oc@post', [OcApiController::class, 'post'])
    ->matchStr('url', regex: OpenChatCrawlerConfig::LINE_URL_MATCH_PATTERN)

    ->path('oc/{open_chat_id}/csv')
    ->matchNum('open_chat_id', min: 1);

Route::path('admin/cookie')
    ->match(function (AdminAuthService $adminAuthService, ?string $key) {
        if (!$adminAuthService->registerAdminCookie($key)) {
            return false;
        }
        return redirect();
    });

Route::run();
