<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class StatisticsRepository implements StatisticsRepositoryInterface
{
    public function insertUpdateDailyStatistics(int $open_chat_id, int $member): void
    {
        $query =
            'INSERT INTO
                statistics (open_chat_id, member, date)
            VALUES
                (:open_chat_id, :member, CURDATE()) ON DUPLICATE KEY
            UPDATE
                member = :member';

        DB::execute($query, compact('open_chat_id', 'member'));
    }

    public function getDailyStatisticsByPeriod(int $open_chat_id, int $start_time, int $end_time): array
    {
        // `Y-m-d`から`Y/m/d`に変換する。`Y`が今年の場合は、`Y/`を省略して`m/d`にする。
        $query =
            "SELECT
                DATE_FORMAT(
                    `date`,
                    IF(
                        YEAR(CURDATE()) = YEAR(`date`),
                        '%m/%d',
                        '%Y/%m/%d'
                    )
                ) AS `date`,
                member
            FROM
                statistics
            WHERE
                open_chat_id = :open_chat_id
                AND `date` BETWEEN DATE(FROM_UNIXTIME(:start_time))
                AND DATE(FROM_UNIXTIME(:end_time))
            ORDER BY 
                `date` ASC";

        $result = DB::fetchAll($query, compact('open_chat_id', 'start_time', 'end_time'));

        return [
            'date' => array_column($result, 'date'),
            'member' => array_column($result, 'member')
        ];
    }
}
