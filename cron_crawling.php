<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\Cron\SyncOpenChat;
use App\Services\CronJson\SyncOpenChatState;
use App\Services\Admin\AdminTool;
use App\Services\RankingPosition\RisingPositionCrawling;

set_time_limit(3600 * 4);

if (excludeTime()) {
    exit;
}

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

try {
    $cron->migrate(false);

    $risingPosition->risingPositionCrawling();

    $cron->finalizeMigrate();
    $cron->finalizeUpdate();
} catch (\Throwable $e) {
    $cron->addMessage('SyncOpenChat: ' . $e->__toString());
    AdminTool::sendLineNofity($cron->getMessage());
}

addCronLog($cron->getMessage());
