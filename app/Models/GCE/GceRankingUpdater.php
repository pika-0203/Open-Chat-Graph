<?php

declare(strict_types=1);

namespace App\Models\GCE;

use App\Models\GCE\DBGce as GceVmSql;
use App\Models\SQLite\SqliteRankingExport;

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
                week_id = 0,
                day_id = 0"
        );

        GceVmSql::execute(
            "UPDATE
                open_chat AS oc
                JOIN statistics_ranking_day AS ranking ON oc.id = ranking.open_chat_id
            SET
                oc.day_id = ranking.id"
        );

        GceVmSql::execute(
            "UPDATE
                open_chat AS oc
                JOIN statistics_ranking_week AS ranking ON oc.id = ranking.open_chat_id
            SET
                oc.week_id = ranking.id"
        );
    }
}
