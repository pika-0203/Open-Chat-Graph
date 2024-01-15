<?php

namespace App\Services\CronJson;

class SQLiteBackupCronState extends AbstractBackupCronState
{
    protected string $remotePath = "storage/zip-backup/SQLite";
    protected string $tempZipPath = __DIR__ . "/../../../storage/zip-backup-temp/SQLite";
    protected string $targetLocalPath = __DIR__ . "/../../../storage/SQLite";
}
