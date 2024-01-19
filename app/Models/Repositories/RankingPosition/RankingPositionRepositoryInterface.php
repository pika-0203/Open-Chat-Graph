<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition;

interface RankingPositionRepositoryInterface
{
    /**
     * @param array $rankingHourArray `[['open_chat_id' => int, 'category' => int, 'position => int, 'time' => stirng]]`
     */
    public function insertDailyRankingPosition(array $rankingHourArray): int;

    /**
     * @param array $risingHourArray `[['open_chat_id' => int, 'category' => int, 'position' => int, 'time' => stirng]]`
     */
    public function insertDailyRisingPosition(array $risingHourArray): int;

    /**
     * @param array $totalCount `[['category' => int, 'total_count_rising' => int, 'total_count_ranking' => int, 'time' => string]]`
     */
    public function insertTotalCount(array $totalCount): int;

    public function daleteDailyPosition(int $open_chat_id): void;

    public function mergeDuplicateDailyPosition(int $duplicated_id, int $open_chat_id): void;
}
