<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class StatisticsRepository implements StatisticsRepositoryInterface
{
    public function addStatisticsRecord(int $open_chat_id, int $member): void
    {
        $query =
            'INSERT INTO
                statistics (open_chat_id, member)
            VALUES
                (:open_chat_id, :member)';

        DB::execute($query, compact('open_chat_id', 'member'));
    }

    public function getDailyStatisticsByPeriod(int $open_chat_id, int $start_time, int $end_time): array
    {
        $query =
            'SELECT
                ROUND(MAX(member), 0) AS member,
                DATE(time) AS date
            FROM
                statistics
            WHERE
                open_chat_id = :open_chat_id
                AND time BETWEEN FROM_UNIXTIME(:start_time)
                AND FROM_UNIXTIME(:end_time)
            GROUP BY
                DATE(time)';

        return DB::fetchAll($query, compact('open_chat_id', 'start_time', 'end_time'));
    }
}
