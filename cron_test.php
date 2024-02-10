<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Admin\AdminTool;
use App\Services\RankingPosition\Persistence\RankingPositionHourPersistence;

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
