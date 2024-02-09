<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Cron\SyncOpenChat;
use App\Services\Cron\CronJson\SyncOpenChatState;
use App\Services\Admin\AdminTool;

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
    $syncOpenChat->handle();
} catch (\Throwable $e) {
    AdminTool::sendLineNofity($e->__toString());
    addCronLog($e->__toString());
    exit;
}
