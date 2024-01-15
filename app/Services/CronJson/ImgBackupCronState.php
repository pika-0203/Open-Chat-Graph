<?php

namespace App\Services\CronJson;

class ImgBackupCronState extends AbstractBackupCronState
{
    protected string $remotePath = "storage/zip-backup/img";
    protected string $tempZipPath = __DIR__ . "/../../../storage/zip-backup-temp/img";
    protected string $targetLocalPath = __DIR__ . '/../../../public/oc-img';
}
