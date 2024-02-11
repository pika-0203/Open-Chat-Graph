<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\Statistics;

use App\Models\ExecuteSqlFile;
use App\Models\Importer\SqlInsert;
use App\Models\Repositories\Statistics\StatisticsRankingUpdaterRepositoryInterface;
use App\Models\SQLite\SQLiteStatistics;
use Shadow\DB;
use Shadow\DBInterface;

class SqliteStatisticsRankingUpdaterRepository implements StatisticsRankingUpdaterRepositoryInterface
{
    protected const DAILY_RANKING_SQL = __DIR__ . '/sql/SQLite_StatisticsRankingUpdaterRepository_updateCreateDailyRankingTable.sql';
    protected const PAST_WEEK_RANKING_SQL = __DIR__ . '/sql/SQLite_StatisticsRankingUpdaterRepository_updateCreatePastWeekRankingTable.sql';
    protected const DATE_PLACE_HOLDER = ':DATE_STRING';

    public function __construct(
        private ExecuteSqlFile $execSql,
        private SqlInsert $inserter
    ) {
    }

    public function updateCreateDailyRankingTable(string $date): int
    {
        $result = $this->execSql->execQueries(
            str_replace(self::DATE_PLACE_HOLDER, $date, file_get_contents(self::DAILY_RANKING_SQL)),
            new SQLiteStatistics
        );

        $this->exportProcess('statistics_ranking_day', new DB);
        return $result;
    }

    public function updateCreatePastWeekRankingTable(string $date): int
    {
        $result = $this->execSql->execQueries(
            str_replace(self::DATE_PLACE_HOLDER, $date, file_get_contents(self::PAST_WEEK_RANKING_SQL)),
            new SQLiteStatistics
        );

        $this->exportProcess('statistics_ranking_week', new DB);
        return $result;
    }

    private function exportProcess(string $tableName, DBInterface $db): void
    {
        $data = SQLiteStatistics::fetchAll("SELECT * FROM {$tableName}");
        if (!$data) {
            throw new \RuntimeException("{$tableName} is empty");
        }

        $db->execute("TRUNCATE TABLE {$tableName}");
        $this->inserter->import($db->connect(), $tableName, $data, 10000);
    }
}
