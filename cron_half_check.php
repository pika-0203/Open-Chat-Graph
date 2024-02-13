<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Cron\SyncOpenChat;
use App\Services\Cron\CronJson\SyncOpenChatState;
use App\Services\Admin\AdminTool;

if (app(SyncOpenChatState::class)->isHourlyTaskActive) {
    AdminTool::sendLineNofity('SyncOpenChat: "isHourlyTaskActive" is active');
}

/**
 * @var SyncOpenChat $syncOpenChat
 */
$syncOpenChat = app(SyncOpenChat::class);
try {
    checkLineSiteRobots();
    $syncOpenChat->handleHalfHourCheck();
} catch (\Throwable $e) {
    AdminTool::sendLineNofity($e->__toString());
    addCronLog($e->__toString());
    exit;
}
