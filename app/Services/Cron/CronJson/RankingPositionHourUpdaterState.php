<?php

namespace App\Services\Cron\CronJson;

use Shadow\AbstoractJsonStorageObject;

class RankingPositionHourUpdaterState extends AbstoractJsonStorageObject
{
    public bool $isActive;
}