<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface StatisticsRankingUpdaterRepositoryInterface
{
    /**
     *　統計ランキングのテーブルを最新データで書き換える
     *
     * @param int テーブルのレコード数
     * 
     * @return int 挿入したレコード件数
     */
    public function updateCreateRankingTable(): int;
}
