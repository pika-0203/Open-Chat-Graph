<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition;

interface RankingPositionPageRepositoryInterface
{
    /**
     * @return array `['date' => ['Y-m-d'], 'member' => [int]]` チャート向けにラベルとデータの配列が別けられている連想配列
     */
    public function getDailyRankingPosition(int $open_chat_id): array;

    /**
     * @return array `['date' => ['Y-m-d'], 'member' => [int]]` チャート向けにラベルとデータの配列が別けられている連想配列
     */
    public function getDailyRisingPosition(int $open_chat_id): array;

    /**
     * @return array `['date' => ['Y-m-d'], 'member' => [int]]` チャート向けにラベルとデータの配列が別けられている連想配列
     */
    public function getDailyRankingAllPosition(int $open_chat_id): array;

    /**
     * @return array `['date' => ['Y-m-d'], 'member' => [int]]` チャート向けにラベルとデータの配列が別けられている連想配列
     */
    public function getDailyRisingAllPosition(int $open_chat_id): array;
}
