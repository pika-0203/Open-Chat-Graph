<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Repositories\RankingPosition\HourMemberRankingUpdaterRepositoryInterface;
use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;

class UpdateHourlyMemberRankingService
{
    function __construct(
        private HourMemberRankingUpdaterRepositoryInterface $hourMemberRankingUpdaterRepository,
        private RankingPositionHourRepositoryInterface $rankingPositionHourRepository,
    ) {
    }

    function update()
    {
        $time = $this->rankingPositionHourRepository->getLastHour();
        if (!$time) return;

        $this->hourMemberRankingUpdaterRepository->updateHourRankingTable(new \DateTime($time));
    }
}
