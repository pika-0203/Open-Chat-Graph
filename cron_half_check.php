<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Cron\SyncOpenChat;
use App\Services\Admin\AdminTool;

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
