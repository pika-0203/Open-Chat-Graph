<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\Cron\SyncOpenChat;
use App\Services\CronJson\SyncOpenChatState;
use App\Services\Admin\AdminTool;
use App\Services\OpenChat\RisingPositionCrawling;
use Shadow\DB;

set_time_limit(3600 * 4);

function isUpdateTime()
{
    $currentTime = new DateTime;
    $updateTime = (new DateTime)->setTime(11, 30, 0);
    $updateTimeRange = (new DateTime)->setTime(12, 30, 0);
    return ($currentTime > $updateTime) && ($currentTime < $updateTimeRange);
}

if (isUpdateTime()) {
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
    DB::$pdo = null;

    $risingPosition->risingPositionCrawling();

    $cron->finalizeMigrate();
    $cron->finalizeUpdate();
} catch (\Throwable $e) {
    $cron->addMessage('SyncOpenChat: ' . $e->__toString());
    AdminTool::sendLineNofity($cron->getMessage());
}

addCronLog($cron->getMessage());