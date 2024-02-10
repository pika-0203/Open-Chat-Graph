<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Admin\AdminTool;
use App\Services\RankingPosition\Persistence\RankingPositionHourPersistence;

set_time_limit(3600 * 2);

AdminTool::sendLineNofity('test: start');

/**
 * @var RankingPositionHourPersistence $syncOpenChat
 */
$inst = app(RankingPositionHourPersistence::class);

try {
    $inst->persistStorageFileToDb();
} catch (\Throwable $e) {
    AdminTool::sendLineNofity($e->__toString());
    exit;
}

AdminTool::sendLineNofity('test: end');
