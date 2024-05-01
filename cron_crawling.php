<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Cron\SyncOpenChat;
use App\Services\Admin\AdminTool;

/**
 * @var SyncOpenChat $syncOpenChat
 */
$syncOpenChat = app(SyncOpenChat::class);
try {
    $syncOpenChat->handle();
    addCronLog('End');
} catch (\Throwable $e) {
    addCronLog($e->__toString());
    AdminTool::sendLineNofity($e->__toString());
}
