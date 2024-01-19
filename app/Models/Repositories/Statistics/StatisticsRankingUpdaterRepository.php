<?php

declare(strict_types=1);

namespace App\Models\Repositories\Statistics;

use App\Models\ExecuteSqlFile;

class StatisticsRankingUpdaterRepository implements StatisticsRankingUpdaterRepositoryInterface
{
    protected ExecuteSqlFile $execSql;
    protected const DAILY_RANKING_SQL = __DIR__ . '/sql/StatisticsRankingUpdaterRepository_updateCreateDailyRankingTable.sql';
    protected const PAST_WEEK_RANKING_SQL = __DIR__ . '/sql/StatisticsRankingUpdaterRepository_updateCreatePastWeekRankingTable.sql';

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
