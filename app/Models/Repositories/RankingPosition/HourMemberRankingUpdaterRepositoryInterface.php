<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition;

interface HourMemberRankingUpdaterRepositoryInterface
{
    public function updateHourRankingTable(\DateTime $dateTime): int;
}
