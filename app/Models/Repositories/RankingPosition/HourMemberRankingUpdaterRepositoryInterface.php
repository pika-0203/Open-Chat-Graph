<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition;

interface HourMemberRankingUpdaterRepositoryInterface
{
    /**
     * @param int[] $filters
     */
    public function updateHourRankingTable(\DateTime $dateTime, array $filters): int;
}
