<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\AppConfig;
use App\Services\Cron\SyncOpenChat;
use App\Services\Admin\AdminTool;
use Shared\MimimalCmsConfig;

try {
    if (isset($argv[1]) && $argv[1]) {
        MimimalCmsConfig::$urlRoot = $argv[1];
    }

    /**
     * @var SyncOpenChat $syncOpenChat
     */
    $syncOpenChat = app(SyncOpenChat::class);

    $syncOpenChat->handle(
        isset($argv[2]) && $argv[2] == 'dailyTest',
        isset($argv[3]) && $argv[3] == 'retryDailyTest'
    );
    addCronLog('End');
} catch (\Throwable $e) {
    addCronLog($e->__toString());

    // 6:30以降にリトライした場合は通知
    if (
        $e->getCode() === AppConfig::DAILY_UPDATE_EXCEPTION_ERROR_CODE
        && $syncOpenChat->isAfterRetryNotificationTime()
    ) {
        return;
    }

    AdminTool::sendDiscordNotify($e->__toString());
}
