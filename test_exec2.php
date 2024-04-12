<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Admin\AdminTool;
use App\Services\RankingPosition\Persistence\RankingPositionDailyPersistence;

set_time_limit(3600 * 10);

/**
 * @var RankingPositionDailyPersistence $oc
 */
$oc = app(RankingPositionDailyPersistence::class);

try {
    AdminTool::sendLineNofity('persistHourToDaily start');

    $oc->persistHourToDaily(false);

    AdminTool::sendLineNofity('persistHourToDaily done');
} catch (\Throwable $e) {
    addCronLog($e->__toString());
    AdminTool::sendLineNofity($e->__toString());
}
