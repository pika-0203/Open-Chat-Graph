<?php

namespace App\Services\Cron\CronJson;

use App\Config\AppConfig;

class CommentDataZipBackupCronState extends AbstractBackupCronState
{
    protected string $remotePath = "storage/zip-backup/CommentBackupData";
    protected string $tempZipPath = __DIR__ . "/../../../../storage/zip-backup-temp/CommentBackupData";
    protected string $targetLocalPath = AppConfig::COMMENT_DATA_FILE_PATH;
}
