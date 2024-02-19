<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Services\Admin\AdminTool;
use Shadow\DB;

/**
 * @var StatisticsRepositoryInterface $inst
 */
$inst = app(StatisticsRepositoryInterface::class);
try {
    AdminTool::sendLineNofity('start');

    $r = DB::fetchAll("SELECT id AS open_chat_id, member, DATE(created_at) AS date FROM open_chat WHERE created_at >= '2024-02-20 00:00:00'");

    $inst->insertMember($r);

    AdminTool::sendLineNofity('done: ' . count($r));
} catch (\Throwable $e) {
    addCronLog($e->__toString());
    AdminTool::sendLineNofity($e->__toString());
}
