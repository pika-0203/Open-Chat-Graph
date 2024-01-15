<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\Cron\SyncOpenChat;
use App\Services\CronJson\SyncOpenChatState;
use App\Services\OpenChat\OpenChatCrawlingFromApi;
use App\Services\OpenChat\OpenChatCrawlingFromPage;
use App\Services\Admin\AdminTool;
use App\Services\OpenChat\RisingPositionCrawling;
use App\Services\OpenChat\SubCategory\OpenChatSubCategorySynchronizer;

set_time_limit(3600 * 4);

/**
 * @var SyncOpenChatState $state
 */
$state = app(SyncOpenChatState::class);
if ($state->isActive) {
    AdminTool::sendLineNofity('SyncOpenChat: 二重起動');
    exit;
}

/**
 * @var SyncOpenChat $cron
 */
$cron = app(SyncOpenChat::class, ['state' => $state]);

/**
 * @var RisingPositionCrawling $risingPosition
 */
$risingPosition = app(RisingPositionCrawling::class);

/**
 * @var OpenChatSubCategorySynchronizer $subCategory
 */
$subCategory = app(OpenChatSubCategorySynchronizer::class);

try {
    $cron->migrate();
    $risingPosition->risingPositionCrawling();
    $risingPosition = null;
    
    $cron->update(app(OpenChatCrawlingFromApi::class));
    $cron->update(app(OpenChatCrawlingFromPage::class));

    $cron->finalizeMigrate();
    $cron->finalizeUpdate();
    $cron->finalizeRanking();

    $subCategory->syncSubCategoriesAll();
} catch (\Throwable $e) {
    $cron->addMessage('SyncOpenChat: ' . $e->__toString());
    AdminTool::sendLineNofity($cron->getMessage());
}

error_log($cron->getMessage() . "\n", 3, __DIR__ . '/logs/cron.log');
