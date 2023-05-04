<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class StatisticsRepository implements StatisticsRepositoryInterface
{
    public function insertUpdateDailyStatistics(int $open_chat_id, int $member): void
    {
        // 今日の日付のレコードがあるかどうかをチェック
        $row = DB::fetch(
            'SELECT
                id
            FROM
                statistics
            WHERE
                open_chat_id = :open_chat_id
                AND date = CURDATE()',
            ['open_chat_id' => $open_chat_id]
        );

        if ($row !== false) {
            // 今日の日付のレコードがある場合は更新
            $updateInsertQuery =
                'UPDATE
                    statistics
                SET
                    -- 今日の最大値を記録する
                    member = GREATEST(member, :member)
                WHERE
                    open_chat_id = :open_chat_id
                    AND date = CURDATE()';
        } else {
            // 今日の日付のレコードがない場合は追加
            $updateInsertQuery =
                'INSERT INTO
                    statistics (open_chat_id, member, date)
                VALUES
                    (:open_chat_id, :member, CURDATE())';
        }

        DB::execute($updateInsertQuery, compact('open_chat_id', 'member'));
    }

    public function getDailyStatisticsByPeriod(int $open_chat_id, int $start_time, int $end_time): array
    {
        $query =
            'SELECT
                member,
                date
            FROM
                statistics
            WHERE
                open_chat_id = :open_chat_id
                AND date BETWEEN DATE(FROM_UNIXTIME(:start_time))
                AND DATE(FROM_UNIXTIME(:end_time))';

        return DB::fetchAll($query, compact('open_chat_id', 'start_time', 'end_time'));
    }
}
