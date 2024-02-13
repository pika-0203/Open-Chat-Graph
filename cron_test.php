<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Cron\SyncOpenChat;
use App\Services\Cron\CronJson\SyncOpenChatState;
use App\Services\Admin\AdminTool;

if (app(SyncOpenChatState::class)->isHourlyTaskActive) {
    AdminTool::sendLineNofity('SyncOpenChat: "isHourlyTaskActive" is active');
}

if (app(SyncOpenChatState::class)->isDailyTaskActive) {
    AdminTool::sendLineNofity('SyncOpenChat: "isDailyTaskActive" is active');
}

/**
 * @var SyncOpenChat $syncOpenChat
 */
$syncOpenChat = app(SyncOpenChat::class);
try {
    checkLineSiteRobots();
    $syncOpenChat->dailyTask();
    $syncOpenChat->finalize();
} catch (\Throwable $e) {
    AdminTool::sendLineNofity($e->__toString());
    addCronLog($e->__toString());
    exit;
}
