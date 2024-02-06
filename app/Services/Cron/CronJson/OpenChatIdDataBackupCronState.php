<?php

namespace App\Services\Cron\CronJson;

use App\Config\AppConfig;

class OpenChatIdDataBackupCronState extends AbstractBackupCronState
{
    protected string $remotePath = "storage/zip-backup/OpenChatBackupData";
    protected string $tempZipPath = __DIR__ . "/../../../storage/zip-backup-temp/OpenChatBackupData";
    protected string $targetLocalPath = AppConfig::OPEN_CHAT_ID_DATA_FILE_PATH;
}
