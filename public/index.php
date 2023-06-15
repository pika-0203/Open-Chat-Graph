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

Route::middlewareGroup(\App\Middleware\CronAuth::class)
    ->path('cron@put@options')

    ->path('cron/ocrowcount@put@options')

    ->path('cron/rank@put@options')

    ->path('cron/addoc@put@options')
    ->matchStr('url', 'put');

Route::middlewareGroup(
    \App\Middleware\VerifyCsrfToken::class,
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
    ->matchNum('open_chat_id', min: 1)

    ->path('oc/{open_chat_id}')
    ->matchNum('open_chat_id', min: 1);

Route::run();
