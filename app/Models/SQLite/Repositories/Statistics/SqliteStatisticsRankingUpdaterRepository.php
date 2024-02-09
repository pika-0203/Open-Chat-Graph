<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\Statistics;

use App\Models\ExecuteSqlFile;
use App\Models\Repositories\Statistics\StatisticsRankingUpdaterRepositoryInterface;
use App\Models\SQLite\Repositories\Statistics\SqliteRankingExport;
use App\Models\SQLite\SQLiteStatistics;
use Shadow\DB;

class SqliteStatisticsRankingUpdaterRepository implements StatisticsRankingUpdaterRepositoryInterface
{
    protected const DAILY_RANKING_SQL = __DIR__ . '/sql/SQLite_StatisticsRankingUpdaterRepository_updateCreateDailyRankingTable.sql';
    protected const PAST_WEEK_RANKING_SQL = __DIR__ . '/sql/SQLite_StatisticsRankingUpdaterRepository_updateCreatePastWeekRankingTable.sql';
    protected const DATE_PLACE_HOLDER = ':DATE_STRING';

    public function __construct(
        private ExecuteSqlFile $execSql,
        private SqliteRankingExport $sqliteRankingExport
    ) {
    }

    function test()
    {
        return str_replace(self::DATE_PLACE_HOLDER, '2024-02-09', file_get_contents(self::DAILY_RANKING_SQL));
    }

    public function updateCreateDailyRankingTable(string $date): int
    {
        $result = $this->execSql->execQueries(
            str_replace(self::DATE_PLACE_HOLDER, $date, file_get_contents(self::DAILY_RANKING_SQL)),
            new SQLiteStatistics
        );

        $this->sqliteRankingExport->exportRankingDay(new DB);
        return $result;
    }

    public function updateCreatePastWeekRankingTable(string $date): int
    {
        $result = $this->execSql->execQueries(
            str_replace(self::DATE_PLACE_HOLDER, $date, file_get_contents(self::PAST_WEEK_RANKING_SQL)),
            new SQLiteStatistics
        );

        $this->sqliteRankingExport->exportRankingWeek(new DB);
        return $result;
    }
}
