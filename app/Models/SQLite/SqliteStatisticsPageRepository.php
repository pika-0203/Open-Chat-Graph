<?php

declare(strict_types=1);

namespace App\Models\SQLite;

use App\Models\Repositories\StatisticsPageRepositoryInterface;
use App\Models\SQLite\SQLiteStatistics;

class SqliteStatisticsPageRepository implements StatisticsPageRepositoryInterface
{
    public function getDailyStatisticsByPeriod(int $open_chat_id): array
    {
        // `Y-m-d`から`Y/m/d`に変換する。`Y`が今年の場合は、`Y/`を省略して`m/d`にする。
        $query =
            "SELECT
                CASE
                    WHEN strftime('%Y', :date) = strftime('%Y', `date`)
                    THEN strftime('%m/%d', `date`)
                    ELSE strftime('%Y/%m/%d', `date`)
                END AS `date`,
                member,
                date as realdate
            FROM
                statistics
            WHERE
                open_chat_id = :open_chat_id
            ORDER BY
                `realdate` ASC";

        $date = date('Y-m-d');

        SQLiteStatistics::connect('?mode=ro&nolock=1');
        $result = SQLiteStatistics::fetchAll($query, compact('open_chat_id', 'date'));
        SQLiteStatistics::$pdo = null;

        return [
            'date' => array_column($result, 'date'),
            'member' => array_column($result, 'member')
        ];
    }

    public function getDailyStatisticsAll(int $open_chat_id): array
    {
        $query =
            "SELECT
                strftime('%Y/%m/%d', `date`) AS `date`,
                member
            FROM
                statistics
            WHERE
                open_chat_id = :open_chat_id
            ORDER BY
                `date` ASC";

        SQLiteStatistics::connect('?mode=ro&nolock=1');
        $result = SQLiteStatistics::fetchAll($query, compact('open_chat_id'));
        SQLiteStatistics::$pdo = null;

        return $result;
    }
}
