<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Services\RankingPosition\Crawler\RisingPositionCrawling;
use App\Services\RankingPosition\Persistence\RankingPositionHourPersistence;

class RankingPositionHourUpdater
{
    private RisingPositionCrawling $risingPositionCrawling;
    private RankingPositionHourPersistence $rankingPositionHourPersistence;

    function __construct(
        RisingPositionCrawling $risingPositionCrawling,
        RankingPositionHourPersistence $rankingPositionHourPersistence
    ) {
        $this->risingPositionCrawling = $risingPositionCrawling;
        $this->rankingPositionHourPersistence = $rankingPositionHourPersistence;
    }

    function crawlRisingAndUpdateRankingPositionHourDb()
    {
        $this->risingPositionCrawling->risingPositionCrawling();
        $this->rankingPositionHourPersistence->persistStorageFileToDb();
    }
}
