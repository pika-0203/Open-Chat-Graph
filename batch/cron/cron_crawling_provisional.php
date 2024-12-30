<?php

if (isset($argv[1]) && $argv[1]) {
    define('URL_ROOT', $argv[1]);
}

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Services\Cron\Provisional\SyncOpenChat;
use App\Services\Admin\AdminTool;

try {
    /**
     * @var SyncOpenChat $syncOpenChat
     */
    $syncOpenChat = app(SyncOpenChat::class);
    $syncOpenChat->handle();
    addCronLog('End');
} catch (\Throwable $e) {
    addCronLog($e->__toString());
    AdminTool::sendLineNofity($e->__toString());
}