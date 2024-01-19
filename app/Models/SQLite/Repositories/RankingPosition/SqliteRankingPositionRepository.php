<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\RankingPositionRepositoryInterface;
use App\Models\SQLite\SQLiteInsertImporter;
use App\Models\SQLite\SQLiteRankingPosition;

class SqliteRankingPositionRepository implements RankingPositionRepositoryInterface
{
    private SQLiteInsertImporter $inserter;

    function __construct(SQLiteInsertImporter $inserter)
    {
        $this->inserter = $inserter;
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

    public function mergeDuplicateDailyPosition(int $duplicated_id, int $open_chat_id): void
    {
        $this->mergeDuplicate('ranking', $duplicated_id, $open_chat_id);
        $this->mergeDuplicate('rising', $duplicated_id, $open_chat_id);
    }

    private function mergeDuplicate(string $tableName, int $duplicated_id, int $open_chat_id): void
    {
        $statistics = SQLiteRankingPosition::fetchAll(
            "SELECT 
                category,
                position,
                time
            FROM
                {$tableName}
            WHERE
                open_chat_id = :duplicated_id",
            compact('duplicated_id')
        );

        foreach ($statistics as $stat) {
            SQLiteRankingPosition::execute(
                "INSERT OR IGNORE INTO {$tableName} (open_chat_id, category, position, time)
                VALUES
                    (
                        :open_chat_id,
                        :category,
                        :position,
                        :time
                    )",
                [
                    'open_chat_id' => $open_chat_id,
                    'category' => $stat['category'],
                    'position' => $stat['position'],
                    'time' => $stat['time'],
                ]
            );
        }
    }
}
