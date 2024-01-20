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

    public function __construct(
        private ExecuteSqlFile $execSql,
        private SqliteRankingExport $sqliteRankingExport
    ) {
    }

    public function updateCreateDailyRankingTable(): int
    {
        $result = $this->execSql->execQueries(self::DAILY_RANKING_SQL, new SQLiteStatistics);
        $this->sqliteRankingExport->exportRankingDay(new DB);
        return $result;
    }

    public function updateCreatePastWeekRankingTable(): int
    {
        $result = $this->execSql->execQueries(self::PAST_WEEK_RANKING_SQL, new SQLiteStatistics);
        $this->sqliteRankingExport->exportRankingWeek(new DB);
        return $result;
    }
}
