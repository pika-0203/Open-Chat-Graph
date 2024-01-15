<?php

namespace App\Services\CronJson;

use Shadow\AbstoractJsonStorageObject;

abstract class AbstractBackupCronState extends AbstoractJsonStorageObject
{
    protected string $remotePath;
    protected string $tempZipPath;
    protected string $targetLocalPath;
    protected string $today;
    public bool $active;
    public array $backups;

    function __construct()
    {
        parent::__construct();
        $this->today = date('Y-m-d');
    }

    function __destruct()
    {
        $this->active = false;
        $this->update();
    }

    function isActive(): bool
    {
        return $this->active;
    }

    function setActive(): void
    {
        $this->active = true;
        $this->update();
    }

    function setBackups(array $backups): void
    {
        $this->backups = $backups;
    }

    function getBackups(): array
    {
        return $this->backups;
    }

    function getRemotePath(): string
    {
        return $this->remotePath . '/' . $this->today;
    }

    function getTempZipPath(): string
    {
        return $this->tempZipPath . '/' . $this->today;
    }

    function getTargetLocalPath(): string
    {
        return $this->targetLocalPath;
    }
}
