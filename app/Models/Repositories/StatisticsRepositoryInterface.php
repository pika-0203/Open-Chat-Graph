<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface StatisticsRepositoryInterface
{
    /**
     * 統計のレコードを追加する
     */
    public function addStatisticsRecord(int $open_chat_id, int $member): void;

    /**
     * 日毎の平均メンバー数の統計を取得する
     * 
     * @param int $start_time 過去のどの時点から統計を取得するかをunixtimeで指定する  
     *            * **Example:** `strtotime('-7 day')`
     * 
     * @param int $end_time   どの時点まで取得するかをunixtimeで指定する
     *            * **Example:** `time()`
     * 
     * @return array `[['data' => 'Y-m-d', 'member' => 'string']]`
     */
    public function getDailyStatisticsByPeriod(int $open_chat_id, int $start_time, int $end_time): array;
}
