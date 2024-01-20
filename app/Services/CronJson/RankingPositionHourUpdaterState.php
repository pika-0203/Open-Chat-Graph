<?php

namespace App\Services\CronJson;

use Shadow\AbstoractJsonStorageObject;

class RankingPositionHourUpdaterState extends AbstoractJsonStorageObject
{
    public bool $isActive;
}