<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Services\Cron\CronJson\RankingPositionHourUpdaterState;
use App\Services\RankingPosition\Crawler\RisingPositionCrawling;
use App\Services\RankingPosition\Persistence\RankingPositionHourPersistence;

class RankingPositionHourUpdater
{
    function __construct(
        private RisingPositionCrawling $risingPositionCrawling,
        private RankingPositionHourPersistence $rankingPositionHourPersistence,
        private RankingPositionHourUpdaterState $state
    ) {
        $this->state->isActive = true;
        $this->state->update();
    }

    function __destruct()
    {
        $this->state->isActive = false;
        $this->state->update();
    }

    function crawlRisingAndUpdateRankingPositionHourDb()
    {
        $this->risingPositionCrawling->risingPositionCrawling();
        $this->rankingPositionHourPersistence->persistStorageFileToDb();
    }
}
