<?php

declare(strict_types=1);

namespace App\Models\RankingPositionDB\Repositories;

use App\Models\RankingPositionDB\RankingPositionDB;
use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourPageRepoDto;
use App\Models\Repositories\RankingPosition\RankingPositionHourPageRepositoryInterface;
use App\Services\OpenChat\Enum\RankingType;

class RankingPositionHourPageRepository implements RankingPositionHourPageRepositoryInterface
{
    public function getHourPosition(
        RankingType $type,
        int $open_chat_id,
        int $category,
        int $intervalHour,
        \DateTime $endTime
    ): RankingPositionHourPageRepoDto {
        $firstTime = $this->getModifiedStartTime($endTime, $intervalHour);

        $tableName = $type->value;
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
                ) AS t3
                LEFT JOIN {$tableName} AS t1 ON t1.time = t3.time
                AND t1.open_chat_id = t3.open_chat_id
                AND t1.category = :category
                LEFT JOIN total_count AS t2 ON t1.time = t2.time
                AND t1.category = t2.category
            ORDER BY
                t3.time ASC";

        $result = RankingPositionDB::fetchAll($query, compact('open_chat_id', 'category'));

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

    private function getModifiedStartTime(\DateTime $endTime, int $intervalHour): string
    {
        $time = new \DateTime($endTime->format('Y-m-d H:i:s'));
        $time->modify("- {$intervalHour}hour");
        return $time->format('Y-m-d H:i:s');
    }
}
