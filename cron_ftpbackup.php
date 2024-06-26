<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Admin\AdminTool;
use App\Services\Cron\AccreditationDataZipBackupCron;
use App\Services\Cron\CommentDataZipBackupCron;
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
     * @var OpenChatIdDataZipBackupCron $ocDataBackUp
     */
    $ocDataBackUp = app(OpenChatIdDataZipBackupCron::class);
    $resultOcId = $ocDataBackUp->saveBackupIdEmidArray();
    addCronLog($resultOcId);

    /**
     * @var AccreditationDataZipBackupCron $accreditationBackUp
     */
    $accreditationBackUp = app(AccreditationDataZipBackupCron::class);
    $resultAccreditation = $accreditationBackUp->saveBackup();
    addCronLog($resultAccreditation);

    /**
     * @var CommentDataZipBackupCron $accreditationBackUp
     */
    $commentDataBackUp = app(CommentDataZipBackupCron::class);
    $resultCommentDataBackUp = $commentDataBackUp->saveBackup();
    addCronLog($resultCommentDataBackUp);
    
} catch (\Throwable $e) {
    AdminTool::sendLineNofity('ftpZipBackUp: ' . $e->__toString());
}
