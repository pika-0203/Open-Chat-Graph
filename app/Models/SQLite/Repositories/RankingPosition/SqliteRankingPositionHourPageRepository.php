<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourPageRepoDto;
use App\Models\Repositories\RankingPosition\RankingPositionHourPageRepositoryInterface;
use App\Models\SQLite\SQLiteRankingPositionHour;

class SqliteRankingPositionHourPageRepository implements RankingPositionHourPageRepositoryInterface
{
    public function getHourRankingPositionTimeAsc(string $emid, int $category, int $intervalHour): RankingPositionHourPageRepoDto|false
    {
        return $this->getHourPosition('ranking', $emid, $category, $intervalHour);
    }

    public function getHourRisingPositionTimeAsc(string $emid, int $category, int $intervalHour): RankingPositionHourPageRepoDto|false
    {
        return $this->getHourPosition('rising', $emid, $category, $intervalHour);
    }

    private function getHourPosition(string $tableName, string $emid, int $category, int $intervalHour): RankingPositionHourPageRepoDto|false
    {
        $endTime = $this->getLastTime($category);
        if (!$endTime) {
            return false;
        }

        $firstTime = $this->getModifiedStartTime($endTime, $intervalHour);

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
                        AND time >= '{$firstTime}'
                ) AS t1
                JOIN total_count AS t2 ON t1.time = t2.time
                AND t1.category = t2.category
            ORDER BY
                t1.time ASC";

        $result = SQLiteRankingPositionHour::fetchAll($query, compact('emid', 'category'));
        
        $dto = new RankingPositionHourPageRepoDto;
        $dto->firstTime = $firstTime;
        
        if (!$result) {
            return $dto;
        }

        $dto->time = array_column($result, 'time');
        $dto->position = array_column($result, 'position');
        $dto->totalCount = array_column($result, 'total_count');
        $dto->member = array_column($result, 'member');
        $dto->firstTime = $firstTime;

        return $dto;
    }

    private function getLastTime(int $category): string|false
    {
        return SQLiteRankingPositionHour::fetchColumn(
            "SELECT
                time
            FROM
                total_count
            WHERE
                category = :category
            ORDER BY
                time DESC
            LIMIT
                1",
            compact('category')
        );
    }

    private function getModifiedStartTime(string $endTime, int $intervalHour): string
    {
        $time = new \DateTime($endTime);
        $time->modify("- {$intervalHour}hour");
        return $time->format('Y-m-d H:i:s');
    }
}
