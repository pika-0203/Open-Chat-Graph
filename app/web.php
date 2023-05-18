<?php

/**
 * MimimalCMS 0.1
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */

define('APP_START_TIME', microtime(true));

require_once __DIR__ . '/../vendor/autoload.php';

use Shadow\Kernel\Route;
use Shadow\Kernel\Validator;

Route::middlewareGroup(\App\Middleware\CronAuth::class)
    ->path('cron@put@options')

    ->path('cron/rank@put@options')

    ->path('cron/addoc@put@options')
    ->matchStr('url', 'put');

Route::middlewareGroup(
    \App\Middleware\VerifyCsrfToken::class,
    \App\Middleware\AutoUserLoginMiddleware::class,
)
    ->path('/')

    ->path('ranking/{pageNumber}')
    ->matchNum('pageNumber', min: 1)

    ->path('search')
    ->matchStr('q', maxLen: 40, emptyAble: true)
    ->matchNum('p', min: 1, emptyAble: true)

    ->path('oc@post')
    ->matchStr('url', regex: \App\Config\OpenChatCrawlerConfig::LINE_URL_MATCH_PATTERN)

    ->path('oc/{open_chat_id}/csv')
    ->middleware([\App\Middleware\LoginAuthorization::class])
    ->matchNum('open_chat_id', min: 1)

    ->path('oc/{open_chat_id}')
    ->matchNum('open_chat_id', min: 1)

    // LINEログイン
    ->path('auth/login@post')
    ->middleware([\App\Middleware\AuthenticatedUserRedirect::class])
    ->matchStr('return_to', regex: [URL_STRING_PATTERN, RELATIVE_PATH_PATTERN])

    // LINEログアウト
    ->path('auth/logout@post');

Route::middlewareGroup(\App\Middleware\AuthenticatedUserRedirect::class)
    // LINEログインのコールバックURL
    ->path('auth/callback')
    ->matchStr('state')
    ->match(function ($error, $code) {
        if (Validator::str($error)) {
            return redirect(session('return_to'))
                ->withErrors('login_denied', message: 'ログインがキャンセルされました。');
        }
        return Validator::str($code);
    });

Route::run();
