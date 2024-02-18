<?php

declare(strict_types=1);

namespace App\Models\Repositories\Statistics;

interface StatisticsRankingUpdaterRepositoryInterface
{
    /**
     *　日次統計ランキングのテーブルを指定の日付で書き換える
     *
     * @param string Y-m-d
     * 
     * @return int 挿入したレコード件数
     */
    public function updateCreateDailyRankingTable(string $date);

    /**
     *　過去１週間ランキングのテーブルを指定の日付で書き換える
     *
     * @param string Y-m-d
     * 
     * @return int 挿入したレコード件数
     */
    public function updateCreatePastWeekRankingTable(string $date);
}
