<?php

declare(strict_types=1);

namespace App\Models\Repositories\Statistics;

interface StatisticsPageRepositoryInterface
{
    /**
     * 日毎のメンバー数の統計を取得する
     * 
     * @return array `['date' => ['Y-m-d'], 'member' => [int]]` チャート向けにラベルとデータの配列が別けられている連想配列
     */
    public function getDailyStatisticsByPeriod(int $open_chat_id): array;

    /**
     * 日毎のメンバー数の統計を全て取得する (CSV出力用)
     * 
     * @return array `[['date' => 'Y/m/d', 'member' => int]]`
     */
    public function getDailyStatsByIdForCsv(int $open_chat_id): array;

    /**
     * 日毎のメンバー数の統計を取得する
     * 
     * @return array{ date: string, member: int }[] date: Y-m-d
     */
    public function getDailyMemberStatsDateAsc(int $open_chat_id): array;
}
