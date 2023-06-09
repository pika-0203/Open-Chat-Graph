<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface StatisticsRepositoryInterface
{
    /**
     * 統計のレコードを追加・更新する
     */
    public function insertUpdateDailyStatistics(int $open_chat_id, int $member): void;

    /**
     * 日毎のメンバー数の統計を取得する
     * 
     * @param int $start_time 過去のどの時点から統計を取得するかをunixtimeで指定する  
     *            * **Example:** `strtotime('-7 day')`
     * 
     * @param int $end_time   どの時点まで取得するかをunixtimeで指定する
     *            * **Example:** `time()`
     *
     * @return array `['date' => ['Y-m-d'], 'member' => [int]]` チャート向けにラベルとデータの配列が別けられている連想配列
     */
    public function getDailyStatisticsByPeriod(int $open_chat_id, int $start_time, int $end_time): array;

    /**
     * @return array `[['date' => 'Y-m-d', 'member' => int]]`
     */
    public function getDailyStatisticsAll(int $open_chat_id): array;
}
