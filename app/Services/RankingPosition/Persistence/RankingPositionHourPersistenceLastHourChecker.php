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
    ) {
    }

    function isLastHourPersistenceCompleted(): bool
    {
        $fileTime = $this->rankingPositionStore->getFileDateTime()->format('Y-m-d H:i:s');
        $dbTime = $this->rankingPositionHourRepository->getLastHour();
        return $fileTime === $dbTime;
    }
}
