<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Cron\SyncOpenChat;
use App\Services\Cron\CronJson\SyncOpenChatState;
use App\Services\Admin\AdminTool;
use App\Services\DailyUpdateCronService;
use App\Services\RankingPosition\RankingPositionDailyUpdater;

/* if (app(SyncOpenChatState::class)->isHourlyTaskActive) {
    AdminTool::sendLineNofity('SyncOpenChat: "isHourlyTaskActive" is active');
}

if (app(SyncOpenChatState::class)->isDailyTaskActive) {
    AdminTool::sendLineNofity('SyncOpenChat: "isDailyTaskActive" is active');
}
 */

AdminTool::sendLineNofity('start');

/**
 * @var DailyUpdateCronService $inst
 */
$inst = app(DailyUpdateCronService::class);

try {
    set_time_limit(5400);
    $inst->update();
} catch (\Throwable $e) {
    AdminTool::sendLineNofity($e->__toString());
    addCronLog($e->__toString());
    exit;
}

AdminTool::sendLineNofity('end');