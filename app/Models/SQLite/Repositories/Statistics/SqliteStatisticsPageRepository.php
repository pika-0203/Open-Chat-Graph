<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\Statistics;

use App\Models\Repositories\Statistics\StatisticsPageRepositoryInterface;
use App\Models\SQLite\SQLiteStatistics;

class SqliteStatisticsPageRepository implements StatisticsPageRepositoryInterface
{
    public function getDailyStatisticsByPeriod(int $open_chat_id): array
    {
        $query =
            "SELECT
                date,
                member
            FROM
                statistics
            WHERE
                open_chat_id = :open_chat_id
            ORDER BY
                date ASC";

        SQLiteStatistics::connect(['mode' => '?mode=ro&nolock=1']);
        $result = SQLiteStatistics::fetchAll($query, compact('open_chat_id'));
        SQLiteStatistics::$pdo = null;

        return [
            'date' => array_column($result, 'date'),
            'member' => array_column($result, 'member')
        ];
    }

    public function getDailyMemberStatsDateAsc(int $open_chat_id): array
    {
        $query =
            "SELECT
                date,
                member
            FROM
                statistics
            WHERE
                open_chat_id = :open_chat_id
            ORDER BY
                date ASC";

        SQLiteStatistics::connect(['mode' => '?mode=ro&nolock=1']);
        $result = SQLiteStatistics::fetchAll($query, compact('open_chat_id'));
        SQLiteStatistics::$pdo = null;

        return $result;
    }

    public function getMemberCount(int $open_chat_id, string $date): int|false
    {
        $query =
            "SELECT
                member
            FROM
                statistics
            WHERE
                open_chat_id = {$open_chat_id}
                AND date = '{$date}'";

        return SQLiteStatistics::fetchColumn($query);
    }
}
