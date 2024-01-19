<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\Statistics;

use App\Models\SQLite\SQLiteStatistics;
use App\Models\Importer\SqlInsert;
use Shadow\DBInterface;

class SqliteRankingExport
{
    private SqlInsert $inserter;

    function __construct(SqlInsert $inserter)
    {
        $this->inserter = $inserter;
    }

    function exportRankingDay(DBInterface $db): void
    {
        $this->process('statistics_ranking_day', $db);
    }

    function exportRankingWeek(DBInterface $db): void
    {
        $this->process('statistics_ranking_week', $db);
    }

    private function process(string $tableName, DBInterface $db): void
    {
        $data = SQLiteStatistics::fetchAll("SELECT * FROM {$tableName}");
        if (!$data) {
            throw new \RuntimeException("{$tableName} is empty");
        }

        $db->execute("TRUNCATE TABLE {$tableName}");
        $this->inserter->import($db->connect(), $tableName, $data, 10000);
    }
}
