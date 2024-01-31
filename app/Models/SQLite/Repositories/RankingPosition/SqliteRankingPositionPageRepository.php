<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\Dto\RankingPositionPageRepoDto;
use App\Models\Repositories\RankingPosition\RankingPositionPageRepositoryInterface;
use App\Models\SQLite\SQLiteRankingPosition;

class SqliteRankingPositionPageRepository implements RankingPositionPageRepositoryInterface
{
    function __construct()
    {
        SQLiteRankingPosition::connect('?mode=ro&nolock=1');
    }

    public function getDailyRankingPositionTimeAsc(int $open_chat_id, int $category): RankingPositionPageRepoDto|false
    {
        return $this->getDailyPosition('ranking', $open_chat_id, $category);
    }

    public function getDailyRisingPositionTimeAsc(int $open_chat_id, int $category): RankingPositionPageRepoDto|false
    {
        return $this->getDailyPosition('rising', $open_chat_id, $category);
    }

    private function getDailyPosition(string $tableName, int $open_chat_id, int $category): RankingPositionPageRepoDto|false
    {
        $query =
            "SELECT
                t1.time AS time,
                t1.position AS position,
                t2.total_count_{$tableName} AS total_count
            FROM
                (
                    SELECT
                        *
                    FROM
                        {$tableName}
                    WHERE
                        open_chat_id = :open_chat_id
                        AND category = :category
                ) AS t1
                JOIN total_count AS t2 ON t1.time = t2.time
                AND t1.category = t2.category
            ORDER BY
                t1.time ASC";

        $result = SQLiteRankingPosition::fetchAll($query, compact('open_chat_id', 'category'));
        if (!$result) {
            return false;
        }

        $dto = new RankingPositionPageRepoDto;
        $dto->time = array_column($result, 'time');
        $dto->position = array_column($result, 'position');
        $dto->totalCount = array_column($result, 'total_count');

        return $dto;
    }
}
