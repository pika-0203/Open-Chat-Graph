<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Admin\AdminTool;
use App\Controllers\Cron\ZipBackupCron;
use App\Services\CronJson\SQLiteBackupCronState;

set_time_limit(3600 * 4);

try {
    /**
     * @var ZipBackupCron $cron
     */
    $cron = app(ZipBackupCron::class);

    $result = $cron->ftpZipBackUp(app(SQLiteBackupCronState::class));
    addCronLog($result);
} catch (\Throwable $e) {
    AdminTool::sendLineNofity('ftpZipBackUp: ' . $e->__toString());
}
