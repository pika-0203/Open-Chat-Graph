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
use App\Controllers\Api\MyListApiController;
use App\Controllers\Pages\OpenChatPageController;
use App\Controllers\Pages\RankingBanLabsPageController;
use App\Controllers\Pages\ReactRankingPageController;
use App\Controllers\Pages\RecentOpenChatPageController;
use App\Controllers\Pages\RecommendOpenChatPageController;
use App\Controllers\Pages\RegisterOpenChatPageController;
use App\Controllers\Pages\TagLabsPageController;
use App\Middleware\VerifyCsrfToken;
use Shadow\Kernel\Reception;

Route::middlewareGroup(RedirectLineWebBrowser::class)
    ->path('ranking/{category}', [ReactRankingPageController::class, 'ranking'])
    ->matchNum('category', min: 1)
    ->match(function (int $category) {
        handleRequestWithETagAndCache(getHouryUpdateTime() . "ranking/{$category}");
        return isset(array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category]);
    })

    ->path('ranking', [ReactRankingPageController::class, 'ranking'])
    ->matchNum('category', emptyAble: true)
    ->match(fn () => handleRequestWithETagAndCache(getHouryUpdateTime() . "ranking"));

Route::path('policy');

Route::path('/')
    ->match(fn () => handleRequestWithETagAndCache(getHouryUpdateTime() . 'index'));

Route::path('oc/{open_chat_id}', [OpenChatPageController::class, 'index'])
    ->matchNum('open_chat_id', min: 1)
    ->match(fn (int $open_chat_id) => handleRequestWithETagAndCache(getHouryUpdateTime() . $open_chat_id));

Route::path('oc/{open_chat_id}/admin', [OpenChatPageController::class, 'index'])
    ->matchNum('open_chat_id', min: 1)
    ->match(
        function (AdminAuthService $adminAuthService) {
            sessionStart();
            return $adminAuthService->auth() ? ['isAdmin' => '1'] : false;
        }
    );

Route::path('oc/{open_chat_id}/csv', [OpenChatPageController::class, 'csv'])
    ->matchNum('open_chat_id', min: 1)
    ->match(fn (int $open_chat_id) => handleRequestWithETagAndCache(getDailyUpdateTime() . $open_chat_id));

Route::path('oclist', [OpenChatRankingPageApiController::class, 'index'])
    ->match(fn (Reception $reception) => handleRequestWithETagAndCache(getHouryUpdateTime() . json_encode($reception->input())));

Route::path(
    'oc/{open_chat_id}/position',
    [RankingPositionApiController::class, 'rankingPosition']
)
    ->matchNum('open_chat_id', min: 1)
    ->matchNum('category', min: 0, max: 41)
    ->matchStr('sort', regex: ['ranking', 'rising'])
    ->matchStr('start_date')
    ->matchStr('end_date')
    ->match(function (string $start_date, string $end_date, Reception $reception) {
        $isValid = $start_date === date("Y-m-d", strtotime($start_date))
            && $end_date === date("Y-m-d", strtotime($end_date));
        if (!$isValid)
            return false;

        handleRequestWithETagAndCache(getHouryUpdateTime() . json_encode($reception->input()));
        return true;
    });

Route::path(
    'oc/{open_chat_id}/position_hour',
    [RankingPositionApiController::class, 'rankingPositionHour']
)
    ->matchNum('open_chat_id', min: 1)
    ->matchNum('category', min: 0, max: 41)
    ->matchStr('sort', regex: ['ranking', 'rising'])
    ->match(function (Reception $reception) {
        handleRequestWithETagAndCache(getHouryUpdateTime() . json_encode($reception->input()));
    });

Route::path('mylist-api', [MyListApiController::class, 'index']);

Route::path('recommend', [RecommendOpenChatPageController::class, 'index'])
    ->matchStr('tag', maxLen: 100)
    ->match(function (string $tag) {
        handleRequestWithETagAndCache(getHouryUpdateTime() . $tag);
    });

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
    ->match(function (int $page) {
        handleRequestWithETagAndCache(getHouryUpdateTime() . "recently-registered/{$page}");
    });

Route::path(
    'recently-registered@get',
    [RecentOpenChatPageController::class, 'index'],
)
    ->matchNum('page', emptyAble: true)
    ->match(function () {
        handleRequestWithETagAndCache(getHouryUpdateTime() . "recently-registered");
    });

Route::path('admin/cookie')
    ->match(function (AdminAuthService $adminAuthService, ?string $key) {
        sessionStart();
        if (!$adminAuthService->registerAdminCookie($key)) {
            return false;
        }
        return redirect();
    });

Route::path(
    'labs/tags',
    [TagLabsPageController::class, 'index']
)
    ->match(function () {
        handleRequestWithETagAndCache(getHouryUpdateTime() . "labs/tags");
    });

Route::path(
    'labs/publication-analytics',
    [RankingBanLabsPageController::class, 'index']
)
    ->matchNum('publish', min: 0, max: 2, default: 1, emptyAble: true)
    ->matchNum('change', min: 0, max: 2, default: 1, emptyAble: true)
    ->matchNum('percent', min: 1, max: 100, default: 50, emptyAble: true)
    ->matchNum('page', min: 1, default: 1, emptyAble: true)
    ->matchStr('keyword', maxLen: 100, emptyAble: true)
    ->match(function (Reception $reception) {
        handleRequestWithETagAndCache(getHouryUpdateTime() . json_encode($reception->input()));
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
    )
    ->middleware([VerifyCsrfToken::class]);

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

cache();
Route::run();
