<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

//class StatisticsPageRepository implements StatisticsPageRepositoryInterface
{
    public function getDailyStatisticsByPeriod(int $open_chat_id): array
    {
        // `Y-m-d`から`Y/m/d`に変換する。`Y`が今年の場合は、`Y/`を省略して`m/d`にする。
        $query =
            "SELECT
                DATE_FORMAT(
                    `date`,
                    IF(
                        YEAR(:date) = YEAR(`date`),
                        '%m/%d',
                        '%Y/%m/%d'
                    )
                ) AS `date`,
                member,
                date as realdate
            FROM
                statistics
            WHERE
                open_chat_id = :open_chat_id
            ORDER BY
                `realdate` ASC";

        $date = date('Y-m-d');
        $result = DB::fetchAll($query, compact('open_chat_id', 'date'));

        return [
            'date' => array_column($result, 'date'),
            'member' => array_column($result, 'member')
        ];
    }

    public function getDailyStatisticsAll(int $open_chat_id): array
    {
        $query =
            "SELECT
                DATE_FORMAT(`date`, '%Y/%m/%d') AS `date`,
                member
            FROM
                statistics
            WHERE
                open_chat_id = :open_chat_id
            ORDER BY
                `date` ASC";

        return DB::fetchAll($query, compact('open_chat_id'));
    }
}
