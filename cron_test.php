<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\SQLite\SQLiteStatistics;
use App\Services\Admin\AdminTool;
use Shadow\DB;

AdminTool::sendLineNofity('start');

try {
    $query =
        "SELECT
            id
        FROM
            open_chat
        WHERE
            created_at > '2024-02-15'";

    $ids = DB::fetchAll($query, null, [\PDO::FETCH_COLUMN, 0]);

    $result = 0;
    foreach ($ids as $id) {
        $r = SQLiteStatistics::executeAndCheckResult(
            "DELETE FROM statistics WHERE open_chat_id = {$id} AND date = '2024-02-14'"
        );
        if ($r) $result++;
    }

    AdminTool::sendLineNofity('done:' . $result);
} catch (\Throwable $e) {
    AdminTool::sendLineNofity($e->__toString());
    addCronLog($e->__toString());
    exit;
}

AdminTool::sendLineNofity('end');
