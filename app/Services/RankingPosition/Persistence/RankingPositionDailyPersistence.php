<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Persistence;

use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;
use App\Models\Repositories\RankingPosition\RankingPositionRepositoryInterface;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class RankingPositionDailyPersistence
{
    private string $date;

    function __construct(
        private RankingPositionHourRepositoryInterface $rankingPositionHourRepository,
        private RankingPositionRepositoryInterface $rankingPositionRepository,
    ) {
        $this->date = OpenChatServicesUtility::getCronModifiedStatsMemberDate();
    }

    function persistHourToDaily(): void
    {
        if ($this->rankingPositionRepository->getLastDate() === $this->date) {
            return;
        }

        $date = new \DateTime($this->date);

        $this->insert(
            $date,
            $this->rankingPositionHourRepository->getDaliyRanking(...),
            $this->rankingPositionRepository->insertDailyRankingPosition(...)
        );

        $this->insert(
            $date,
            $this->rankingPositionHourRepository->getDailyRising(...),
            $this->rankingPositionRepository->insertDailyRisingPosition(...)
        );

        $this->rankingPositionRepository->insertTotalCount(
            $this->rankingPositionHourRepository->getTotalCount($date)
        );
    }

    private function insert(\DateTime $date, \Closure $getter, \Closure $inserter)
    {
        $inserter($getter($date), $this->date);
        $inserter($getter($date, true), $this->date);
    }
}
