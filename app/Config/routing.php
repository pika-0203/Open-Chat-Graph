<?php

namespace App\Config;

use Shadow\Kernel\Route;
use App\Middleware\RedirectLineWebBrowser;
use App\Services\Admin\AdminAuthService;
use App\Controllers\Api\OpenChatRankingPageApiController;
use App\Controllers\Api\RankingPositionApiController;
use App\Controllers\Pages\OpenChatPageController;
use App\Controllers\Pages\ReactRankingPageController;
use App\Middleware\AdminAuth;

Route::middlewareGroup(RedirectLineWebBrowser::class)
    ->path('ranking', [ReactRankingPageController::class, 'ranking'])
    ->match(cache(...))

    ->path('ranking/{category}', [ReactRankingPageController::class, 'ranking'])
    ->match(cache(...));

Route::path('member')
    ->match(cache(...));

Route::path('recent')
    ->match(cache(...));

Route::path('recent/{pageNumber}')
    ->matchNum('pageNumber', min: 1)
    ->match(cache(...));

Route::path('oc/{open_chat_id}', [OpenChatPageController::class, 'index'])
    ->matchNum('open_chat_id', min: 1);

Route::path('oclist', [OpenChatRankingPageApiController::class, 'index']);

Route::path(
    'oc/{open_chat_id}/position',
    [RankingPositionApiController::class, 'rankingPosition']
)
    ->matchNum('open_chat_id', min: 1)
    ->matchStr('sort', regex: ['ranking', 'ranking_all', 'rising', 'rising_all']);


Route::path('/')
    ->middleware([RedirectLineWebBrowser::class]);

Route::path('oc/{open_chat_id}/csv', [OpenChatPageController::class, 'csv'])
    ->matchNum('open_chat_id', min: 1);

Route::path('admin/cookie')
    ->match(function (AdminAuthService $adminAuthService, ?string $key) {
        if (!$adminAuthService->registerAdminCookie($key)) {
            return false;
        }
        return redirect();
    });

// 旧URLからのリダイレクト先
Route::path('search')
    ->matchStr('q', maxLen: 40, emptyAble: true)
    ->match(fn (string $q) => redirect('ranking/?' . http_build_query(['keyword' => $q, 'list' => 'all']), 301));

Route::path('react-test')
    ->match(redirect('ranking'));

Route::path('react-test/{category}')
    ->match(fn ($category) => redirect('ranking/' . $category . strstr($_SERVER['REQUEST_URI'] ?? '', '?')));

Route::run(AdminAuth::class);
