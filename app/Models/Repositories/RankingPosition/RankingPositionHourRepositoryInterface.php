<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition;

interface RankingPositionHourRepositoryInterface
{
    public function insertRankingHourFromDtoArray(string $fileTime, array $insertDtoArray): int;

    public function insertRisingHourFromDtoArray(string $fileTime, array $insertDtoArray): int;

    public function insertHourMemberFromDtoArray(string $fileTime, array $insertDtoArray): int;

    /**
     * @return array{ open_chat_id: int, category: int, position: int, time: stirng }[]
     */
    public function getMinRankingHour(\DateTime $date, bool $all = false): array;

    /**
     * @return array{ open_chat_id: int, category: int, position: int, time: stirng }[]
     */
    public function getMinRisingHour(\DateTime $date, bool $all = false): array;

    /**
     * @return array{ category: int, total_count_rising: int, total_count_ranking: int, time: string }
     */
    public function getTotalCount(\DateTime $date, bool $isDate = true): array;

    public function dalete(\DateTime $date): void;

    public function insertTotalCount(string $fileTime): int;
}
