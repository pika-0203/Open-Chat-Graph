<?php

declare(strict_types=1);

namespace App\Models\GCE;

use App\Models\GCE\DBGce as GceVmSql;
use App\Models\SQLite\Repositories\Statistics\SqliteRankingExport;

class GceRankingUpdater
{
    private SqliteRankingExport $sqliteRankingExport;

    function __construct(SqliteRankingExport $sqliteRankingExport)
    {
        $this->sqliteRankingExport = $sqliteRankingExport;
    }

    function updateRanking(): void
    {
        $this->sqliteRankingExport->exportRankingDay(new GceVmSql);
        $this->sqliteRankingExport->exportRankingWeek(new GceVmSql);
        $this->finalize();
    }

    private function finalize(): void
    {
        GceVmSql::execute(
            "UPDATE
                open_chat
            SET
                statistics_ranking_day_id = 0,
                statistics_ranking_day_diff_member = 0,
                statistics_ranking_day_percent_increase = 0,
                statistics_ranking_week_id = 0,
                statistics_ranking_week_diff_member = 0,
                statistics_ranking_week_percent_increase = 0"
        );

        GceVmSql::execute(
            "UPDATE
                open_chat AS oc
                JOIN statistics_ranking_day AS r ON oc.id = r.open_chat_id
            SET
                oc.statistics_ranking_day_id = r.id,
                oc.statistics_ranking_day_diff_member = r.diff_member,
                oc.statistics_ranking_day_percent_increase = r.percent_increase"
        );

        GceVmSql::execute(
            "UPDATE
                open_chat AS oc
                JOIN statistics_ranking_week AS r ON oc.id = r.open_chat_id
            SET
                oc.statistics_ranking_week_id = r.id,
                oc.statistics_ranking_week_diff_member = r.diff_member,
                oc.statistics_ranking_week_percent_increase = r.percent_increase"
        );
    }
}
