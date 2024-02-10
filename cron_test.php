<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Admin\AdminTool;
use App\Services\RankingPosition\RankingPositionDailyUpdater;

set_time_limit(3600 * 2);

AdminTool::sendLineNofity('test: start');

/**
 * @var RankingPositionDailyUpdater $inst
 */
$inst = app(RankingPositionDailyUpdater::class);

try {
    $inst->updateYesterdayDailyDb();
} catch (\Throwable $e) {
    AdminTool::sendLineNofity($e->__toString());
    exit;
}

AdminTool::sendLineNofity('test: end');
