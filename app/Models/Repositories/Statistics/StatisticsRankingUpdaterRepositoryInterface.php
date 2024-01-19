<?php

declare(strict_types=1);

namespace App\Models\Repositories\Statistics;

interface StatisticsRankingUpdaterRepositoryInterface
{
    /**
     *　日次統計ランキングのテーブルを最新データで書き換える
     *
     * @param int テーブルのレコード数
     * 
     * @return int 挿入したレコード件数
     */
    public function updateCreateDailyRankingTable(): int;

    /**
     *　過去１週間ランキングのテーブルを最新データで書き換える
     *
     * @param int テーブルのレコード数
     * 
     * @return int 挿入したレコード件数
     */
    public function updateCreatePastWeekRankingTable(): int;
}
