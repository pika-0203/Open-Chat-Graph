<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourPageRepoDto;
use App\Models\Repositories\RankingPosition\RankingPositionHourPageRepositoryInterface;
use App\Models\SQLite\SQLiteRankingPositionHour;

class SqliteRankingPositionHourPageRepository implements RankingPositionHourPageRepositoryInterface
{
    function __construct()
    {
        SQLiteRankingPositionHour::connect('?mode=ro&nolock=1');
    }

    public function getHourRankingPositionTimeAsc(string $emid, int $category): RankingPositionHourPageRepoDto|false
    {
        return $this->getHourPosition('ranking', $emid, $category);
    }

    public function getHourRisingPositionTimeAsc(string $emid, int $category): RankingPositionHourPageRepoDto|false
    {
        return $this->getHourPosition('rising', $emid, $category);
    }

    private function getHourPosition(string $tableName, string $emid, int $category): RankingPositionHourPageRepoDto|false
    {
        $time = new \DateTime();
        $time->modify('- 25hour');
        $timeString = $time->format('Y-m-d H:i:s');

        $query =
            "SELECT
                t1.time AS time,
                t1.position AS position,
                t1.member AS member,
                t2.total_count_{$tableName} AS total_count
            FROM
                (
                    SELECT
                        *
                    FROM
                        {$tableName}
                    WHERE
                        emid = :emid
                        AND category = :category
                        AND time > '{$timeString}'
                ) AS t1
                JOIN total_count AS t2 ON t1.time = t2.time
                AND t1.category = t2.category
            ORDER BY
                t1.time ASC";

        $result = SQLiteRankingPositionHour::fetchAll($query, compact('emid', 'category'));
        if (!$result) {
            return false;
        }

        $dto = new RankingPositionHourPageRepoDto;
        $dto->time = array_column($result, 'time');
        $dto->position = array_column($result, 'position');
        $dto->totalCount = array_column($result, 'total_count');
        $dto->member = array_column($result, 'member');

        return $dto;
    }
}
