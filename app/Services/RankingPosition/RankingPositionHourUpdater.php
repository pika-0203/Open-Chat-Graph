<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Services\CronJson\RankingPositionHourUpdaterState;
use App\Services\RankingPosition\Crawler\RisingPositionCrawling;
use App\Services\RankingPosition\Persistence\RankingPositionHourPersistence;

class RankingPositionHourUpdater
{
    private RisingPositionCrawling $risingPositionCrawling;
    private RankingPositionHourPersistence $rankingPositionHourPersistence;
    private RankingPositionHourUpdaterState $state;

    function __construct(
        RisingPositionCrawling $risingPositionCrawling,
        RankingPositionHourPersistence $rankingPositionHourPersistence,
        RankingPositionHourUpdaterState $state
    ) {
        $this->risingPositionCrawling = $risingPositionCrawling;
        $this->rankingPositionHourPersistence = $rankingPositionHourPersistence;
        $this->state = $state;

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
