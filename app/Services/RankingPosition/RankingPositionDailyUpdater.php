<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Services\RankingPosition\Persistence\RankingPositionDailyPersistence;

class RankingPositionDailyUpdater
{
    function __construct(
        private RankingPositionDailyPersistence $rankingPositionDailyPersistence
    ) {
    }

    function updateYesterdayRankingPositionDailyDb()
    {
        $this->rankingPositionDailyPersistence->persistHourToDaily();
    }
}
