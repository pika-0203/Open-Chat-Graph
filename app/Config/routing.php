<?php

namespace App\Config;

use App\Controllers\Api\AdminEndPointController;
use App\Controllers\Api\CommentLikePostApiController;
use App\Controllers\Api\CommentListApiController;
use App\Controllers\Api\CommentPostApiController;
use App\Controllers\Api\CommentReportApiController;
use Shadow\Kernel\Route;
use App\Middleware\RedirectLineWebBrowser;
use App\Services\Admin\AdminAuthService;
use App\Controllers\Api\OpenChatRankingPageApiController;
use App\Controllers\Api\OpenChatRegistrationApiController;
use App\Controllers\Api\RankingPositionApiController;
use App\Controllers\Pages\OpenChatPageController;
use App\Controllers\Pages\ReactRankingPageController;
use App\Controllers\Pages\RecentOpenChatPageController;
use App\Controllers\Pages\RecommendOpenChatPageController;
use App\Controllers\Pages\RegisterOpenChatPageController;
use App\Middleware\AdminCookieValidation;
use App\Middleware\VerifyCsrfToken;

Route::middlewareGroup(RedirectLineWebBrowser::class)
    ->path('ranking/{category}', [ReactRankingPageController::class, 'ranking'])
    ->matchNum('category', min: 1)
    ->match(fn (int $category) => isset(array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category]))
    ->match(cache(...))

    ->path('ranking', [ReactRankingPageController::class, 'ranking'])
    ->match(cache(...));

Route::path('policy')
    ->middleware([VerifyCsrfToken::class]);

Route::path('oc/{open_chat_id}', [OpenChatPageController::class, 'index'])
    ->matchNum('open_chat_id', min: 1)
    ->match(cache(...))
    ->middleware([
        AdminCookieValidation::class,
        VerifyCsrfToken::class,
    ]);

Route::path('oc/{open_chat_id}/csv', [OpenChatPageController::class, 'csv'])
    ->matchNum('open_chat_id', min: 1);

Route::path('oclist', [OpenChatRankingPageApiController::class, 'index']);

Route::path(
    'oc/{open_chat_id}/position',
    [RankingPositionApiController::class, 'rankingPosition']
)
    ->matchNum('open_chat_id', min: 1)
    ->matchNum('category', min: 0, max: 41)
    ->matchStr('sort', regex: ['ranking', 'rising'])
    ->matchStr('start_date')
    ->matchStr('end_date')
    ->match(function (string $start_date, string $end_date) {
        return $start_date === date("Y-m-d", strtotime($start_date))
            && $end_date === date("Y-m-d", strtotime($end_date));
    });

Route::path(
    'oc/{open_chat_id}/position_hour',
    [RankingPositionApiController::class, 'rankingPositionHour']
)
    ->matchNum('open_chat_id', min: 1)
    ->matchNum('category', min: 0, max: 41)
    ->matchStr('sort', regex: ['ranking', 'rising']);

Route::path('/')
    ->match(cache(...));

Route::path('recommend', [RecommendOpenChatPageController::class, 'index'])
    ->matchStr('tag', maxLen: 100);

Route::path(
    'oc@post@get',
    [OpenChatRegistrationApiController::class, 'register', 'post'],
    [RegisterOpenChatPageController::class, 'index', 'get'],
)
    ->middleware([VerifyCsrfToken::class])
    ->matchStr('url', 'post', regex: OpenChatCrawlerConfig::LINE_URL_MATCH_PATTERN);

Route::path(
    'recently-registered/{page}@get',
    [RecentOpenChatPageController::class, 'index'],
)
    ->matchNum('page')
    ->match(cache(...));

Route::path(
    'recently-registered@get',
    [RecentOpenChatPageController::class, 'index'],
)
    ->match(cache(...));

Route::path('admin/cookie')
    ->match(function (AdminAuthService $adminAuthService, ?string $key) {
        if (!$adminAuthService->registerAdminCookie($key)) {
            return false;
        }
        return redirect();
    });

// コメントAPI
Route::path(
    'comment/{open_chat_id}@get@post',
    [CommentListApiController::class, 'index', 'get'],
    [CommentPostApiController::class, 'index', 'post']
)
    ->matchNum('open_chat_id', min: 0)
    ->matchNum('page', 'get', min: 0)
    ->matchNum('limit', 'get', min: 1)
    ->matchStr('token', 'post')
    ->matchStr('name', 'post', maxLen: 20, emptyAble: true)
    ->matchStr('text', 'post', maxLen: 1000)
    ->match(
        fn (string $text, string $name) => removeAllZeroWidthCharacters($text)
            ? ['name' => removeAllZeroWidthCharacters($name) ? $name : '']
            : false,
        'post'
    );

// コメントリアクションAPI
Route::path(
    'comment_reaction/{comment_id}@post@delete',
    [CommentLikePostApiController::class, 'add', 'post'],
    [CommentLikePostApiController::class, 'delete', 'delete']
)
    ->matchNum('comment_id', min: 1)
    ->matchStr('type', 'post', regex: ['empathy', 'insights', 'negative'])
    ->middleware([VerifyCsrfToken::class]);

// 通報API
Route::path(
    'comment_report/{comment_id}@post',
    [CommentReportApiController::class, 'index']
)
    ->matchNum('comment_id', min: 1)
    ->matchStr('token');

Route::path(
    'admin-api@post',
    [AdminEndPointController::class, 'index']
);

Route::path(
    'admin-api/deletecomment@post@get',
    [AdminEndPointController::class, 'deletecomment']
)
    ->matchNum('id')
    ->matchNum('commentId')
    ->matchNum('flag', min: 0, max: 3);

Route::run();
