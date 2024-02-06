<?php

namespace App\Services\Cron\CronJson;

use Shadow\AbstoractJsonStorageObject;

class SyncOpenChatState extends AbstoractJsonStorageObject
{
    public bool $isActive;
}