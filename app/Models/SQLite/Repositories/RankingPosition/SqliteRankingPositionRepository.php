<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\RankingPositionRepositoryInterface;
use App\Models\SQLite\SQLiteInsertImporter;
use App\Models\SQLite\SQLiteRankingPosition;

class SqliteRankingPositionRepository implements RankingPositionRepositoryInterface
{
    function __construct(
        private SQLiteInsertImporter $inserter
    ) {
    }

    public function insertDailyRankingPosition(array $rankingHourArray): int
    {
        return $this->inserter->import(SQLiteRankingPosition::connect(), 'ranking', $rankingHourArray, 500);
    }

    public function insertDailyRisingPosition(array $risingHourArray): int
    {
        return $this->inserter->import(SQLiteRankingPosition::connect(), 'rising', $risingHourArray, 500);
    }

    public function insertTotalCount(array $totalCount): int
    {
        return $this->inserter->import(SQLiteRankingPosition::connect(), 'total_count', $totalCount, 500);
    }

    public function daleteDailyPosition(int $open_chat_id): void
    {
        SQLiteRankingPosition::execute(
            "DELETE FROM
                rising
            WHERE
                open_chat_id = :open_chat_id",
            compact('open_chat_id')
        );

        SQLiteRankingPosition::execute(
            "DELETE FROM
                ranking
            WHERE
                open_chat_id = :open_chat_id",
            compact('open_chat_id')
        );
    }
}
