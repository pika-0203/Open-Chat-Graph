<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Admin\AdminTool;
use App\Services\Cron\ZipBackupCron;
use App\Services\Cron\CronJson\SQLiteBackupCronState;
use App\Services\Cron\OpenChatIdDataZipBackupCron;

set_time_limit(3600 * 4);

try {
    /**
     * @var ZipBackupCron $cron
     */
    $cron = app(ZipBackupCron::class);
    $resultSqlite = $cron->ftpZipBackUp(app(SQLiteBackupCronState::class));
    addCronLog($resultSqlite);

    /**
     * @var OpenChatIdDataZipBackupCron $ocData
     */
    $ocData = app(OpenChatIdDataZipBackupCron::class);
    $resultOcId = $ocData->saveBackupIdEmidArray();
    addCronLog($resultOcId);
} catch (\Throwable $e) {
    AdminTool::sendLineNofity('ftpZipBackUp: ' . $e->__toString());
}
