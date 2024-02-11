<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Persistence;

use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;
use App\Services\RankingPosition\Store\RankingPositionStore;

class RankingPositionHourPersistenceLastHourChecker
{
    function __construct(
        private RankingPositionHourRepositoryInterface $rankingPositionHourRepository,
        private RankingPositionStore $rankingPositionStore,
        private RankingPositionHourPersistence $rankingPositionHourPersistence,
    ) {
    }

    function checkLastHour(): void
    {
        $fileTime = $this->rankingPositionStore->getFileDateTime()->format('Y-m-d H:i:s');
        $dbTime = $this->rankingPositionHourRepository->getLastHour();
        if ($fileTime === $dbTime) {
            return;
        }

        addCronLog('HourPersistence RETRY');
        $this->rankingPositionHourPersistence->persistStorageFileToDb();
    }
}
