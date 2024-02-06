<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Cron\SyncOpenChat;
use App\Services\Cron\CronJson\SyncOpenChatState;
use App\Services\OpenChat\OpenChatCrawlingFromApi;
use App\Services\Admin\AdminTool;
use App\Services\Cron\CronJson\RankingPositionHourUpdaterState;
use App\Services\OpenChat\SubCategory\OpenChatSubCategorySynchronizer;
use App\Services\RankingPosition\RankingPositionHourUpdater;

set_time_limit(3600 * 4);

if (app(SyncOpenChatState::class)->isActive) {
    AdminTool::sendLineNofity('SyncOpenChat: state is active');
    exit;
}

/**
 * @var SyncOpenChat $syncOpenChat
 */
$syncOpenChat = app(SyncOpenChat::class);
try {
    checkLineSiteRobots();
    $syncOpenChat->migrate();
    $syncOpenChat->update(app(OpenChatCrawlingFromApi::class));
    $syncOpenChat->finalizeUpdate();

    $syncOpenChat->finalizeRanking();
} catch (\Throwable $e) {
    $syncOpenChat->addMessage('SyncOpenChat: ' . $e->__toString());
    AdminTool::sendLineNofity($syncOpenChat->getMessage());
    exit;
}

addCronLog($syncOpenChat->getMessage());
unset($syncOpenChatState);
unset($syncOpenChat);

if (app(RankingPositionHourUpdaterState::class)->isActive) {
    AdminTool::sendLineNofity('RankingPositionHourUpdater: state is active');
    exit;
}

/**
 * @var RankingPositionHourUpdater $rankingPosition
 */
$rankingPosition = app(RankingPositionHourUpdater::class);
try {
    $rankingPosition->crawlRisingAndUpdateRankingPositionHourDb();
} catch (\Throwable $e) {
    AdminTool::sendLineNofity('rankingPosition: ' . $e->__toString());
    exit;
}

unset($rankingPosition);

/**
 * @var OpenChatSubCategorySynchronizer $subCategory
 */
$subCategory = app(OpenChatSubCategorySynchronizer::class);
try {
    $subCategory->syncSubCategoriesAll();
} catch (\Throwable $e) {
    AdminTool::sendLineNofity('subCategory: ' . $e->__toString());
}

unset($subCategory);

updateSitemap();