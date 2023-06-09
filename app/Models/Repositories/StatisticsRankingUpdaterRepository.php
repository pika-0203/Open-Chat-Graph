<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\ExecuteSqlFile;

class StatisticsRankingUpdaterRepository implements StatisticsRankingUpdaterRepositoryInterface
{
    private ExecuteSqlFile $execSql;
    private const DAILY_RANKING_SQL = __DIR__ . '/sql/StatisticsRankingUpdaterRepository_updateCreateDailyRankingTable.sql';
    private const PAST_WEEK_RANKING_SQL = __DIR__ . '/sql/StatisticsRankingUpdaterRepository_updateCreatePastWeekRankingTable.sql';

    public function __construct(ExecuteSqlFile $execSql)
    {
        $this->execSql = $execSql;
    }

    public function updateCreateDailyRankingTable(): int
    {
        return $this->execSql->execute(self::DAILY_RANKING_SQL);
    }

    public function updateCreatePastWeekRankingTable(): int
    {
        return $this->execSql->execute(self::PAST_WEEK_RANKING_SQL);
    }
}
