<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition;

interface RankingPositionRepositoryInterface
{
    /**
     * @param array{ open_chat_id: int, category: int, position: int, time: stirng }[] $rankingHourArray
     */
    public function insertDailyRankingPosition(array $rankingHourArray): int;

    /**
     * @param array{ open_chat_id: int, category: int, position: int, time: stirng }[] $risingHourArray
     */
    public function insertDailyRisingPosition(array $risingHourArray): int;

    /**
     * @param array{ category: int, total_count_rising: int, total_count_ranking: int, time: string } $totalCount
     */
    public function insertTotalCount(array $totalCount): int;

    public function daleteDailyPosition(int $open_chat_id): void;

    public function mergeDuplicateDailyPosition(int $duplicated_id, int $open_chat_id): void;
}
