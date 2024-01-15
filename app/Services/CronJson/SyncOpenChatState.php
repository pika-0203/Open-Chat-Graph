<?php

namespace App\Services\CronJson;

use Shadow\AbstoractJsonStorageObject;

class SyncOpenChatState extends AbstoractJsonStorageObject
{
    public bool $isActive;
}