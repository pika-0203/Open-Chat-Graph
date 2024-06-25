<?php

namespace App\Services\Cron\CronJson;

use App\Config\AppConfig;

class AccreditationDataZipBackupCronState extends AbstractBackupCronState
{
    protected string $remotePath = "storage/zip-backup/AccreditationBackupData";
    protected string $tempZipPath = __DIR__ . "/../../../../storage/zip-backup-temp/AccreditationBackupData";
    protected string $targetLocalPath = AppConfig::ACCREDITATION_DATA_FILE_PATH;
}
