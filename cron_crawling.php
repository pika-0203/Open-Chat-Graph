<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Config\AppConfig;
use App\Services\Cron\SyncOpenChat;
use App\Services\Cron\CronJson\SyncOpenChatState;
use App\Services\Admin\AdminTool;
use App\Services\Cron\CronJson\RankingPositionHourUpdaterState;
use App\Services\RankingPosition\RankingPositionDailyUpdater;
use App\Services\RankingPosition\RankingPositionHourUpdater;

set_time_limit(3600 * 4);

if (excludeTime()) {
    // 日次処理 12:30の場合
    exit;
}

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
    $syncOpenChat->migrate(false);
    $syncOpenChat->finalizeUpdate();
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

if (!excludeTime([0, AppConfig::CRON_START_MINUTE], [1, AppConfig::CRON_START_MINUTE])) {
    exit;
}

// 日次処理 0:30の場合
/**
 * @var RankingPositionDailyUpdater $rankingPositionDaily
 */
$rankingPositionDaily = app(RankingPositionDailyUpdater::class);
try {
    $rankingPositionDaily->updateYesterdayRankingPositionDailyDb();
} catch (\Throwable $e) {
    AdminTool::sendLineNofity('rankingPositionDaily: ' . $e->__toString());
}

unset($rankingPosition);
