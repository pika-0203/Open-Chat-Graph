<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourPageRepoDto;
use App\Models\Repositories\RankingPosition\RankingPositionHourPageRepositoryInterface;
use App\Models\SQLite\SQLiteRankingPositionHour;

class SqliteRankingPositionHourPageRepository implements RankingPositionHourPageRepositoryInterface
{
    public function getHourRankingPositionTimeAsc(int $open_chat_id, int $category, int $intervalHour): RankingPositionHourPageRepoDto|false
    {
        return $this->getHourPosition('ranking', $open_chat_id, $category, $intervalHour);
    }

    public function getHourRisingPositionTimeAsc(int $open_chat_id, int $category, int $intervalHour): RankingPositionHourPageRepoDto|false
    {
        return $this->getHourPosition('rising', $open_chat_id, $category, $intervalHour);
    }

    private function getHourPosition(string $tableName, int $open_chat_id, int $category, int $intervalHour): RankingPositionHourPageRepoDto|false
    {
        SQLiteRankingPositionHour::connect('?mode=ro&nolock=1');
        $endTime = $this->getLastTime($category);
        if (!$endTime) {
            return false;
        }

        $firstTime = $this->getModifiedStartTime($endTime, $intervalHour);

        $query =
            "SELECT
                t3.time AS time,
                IFNULL(t1.position, 0) AS position,
                t3.member AS member,
                t2.total_count_{$tableName} AS total_count
            FROM
                (
                    SELECT
                        *
                    FROM
                        member
                    WHERE
                        open_chat_id = :open_chat_id
                        AND time >= '{$firstTime}'
                ) AS t3
                LEFT JOIN {$tableName} AS t1 ON t1.time = t3.time
                AND t1.open_chat_id = t3.open_chat_id
                AND t1.category = :category
                LEFT JOIN total_count AS t2 ON t1.time = t2.time
                AND t1.category = t2.category
            ORDER BY
                t3.time ASC";

        $result = SQLiteRankingPositionHour::fetchAll($query, compact('open_chat_id', 'category'));
        SQLiteRankingPositionHour::$pdo = null;

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
