<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Persistence;

use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;
use App\Models\Repositories\RankingPosition\RankingPositionRepositoryInterface;
use DateTime;

class RankingPositionDailyPersistence
{
    function __construct(
        private RankingPositionHourRepositoryInterface $rankingPositionHourRepository,
        private RankingPositionRepositoryInterface $rankingPositionRepository,
    ) {
    }

    function persistHourToDaily(\DateTime $date = new DateTime('yesterday')): void
    {
        $this->insert(
            $date,
            $this->rankingPositionHourRepository->getMinRankingHour(...),
            $this->rankingPositionRepository->insertDailyRankingPosition(...)
        );

        $this->insert(
            $date,
            $this->rankingPositionHourRepository->getMinRisingHour(...),
            $this->rankingPositionRepository->insertDailyRisingPosition(...)
        );

        $this->rankingPositionRepository->insertTotalCount(
            $this->rankingPositionHourRepository->getTotalCount($date)
        );

        $deleteDate = new DateTime($date->format('Y-m-d'));
        $deleteDate->modify('- 1day');
        $this->rankingPositionHourRepository->dalete($deleteDate);
    }

    private function insert(\DateTime $date, \Closure $getter, \Closure $inserter)
    {
        $inserter($getter($date));
        $inserter($getter($date, true));
    }
}
