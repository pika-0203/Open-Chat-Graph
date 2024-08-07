<?php

declare(strict_types=1);

namespace App\Models\Repositories\Statistics;

interface StatisticsPageRepositoryInterface
{
    /**
     * 日毎のメンバー数の統計を取得する
     * 
     * @return array{ date: string[], member: int[] } チャート向けにラベルとデータの配列が別けられている連想配列
     */
    public function getDailyStatisticsByPeriod(int $open_chat_id): array;

    /**
     * 日毎のメンバー数の統計を取得する
     * 
     * @return array{ date: string, member: int }[] date: Y-m-d
     */
    public function getDailyMemberStatsDateAsc(int $open_chat_id): array;

    /**
     * 指定した日付・IDのメンバー数を取得する
     * 
     * @param string $date Y-m-d
     * 
     * @return int
     */
    public function getMemberCount(int $open_chat_id, string $date): int|false;
}
