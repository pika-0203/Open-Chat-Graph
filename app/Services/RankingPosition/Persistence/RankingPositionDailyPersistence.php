<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Persistence;

use App\Config\AppConfig;
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

        $this->deleteYesterday();
    }

    private function insert(\DateTime $date, \Closure $getter, \Closure $inserter)
    {
        $inserter($getter($date));
        $inserter($getter($date, true));
    }

    private function deleteYesterday()
    {
        $deleteTime = new \DateTime($this->date);

        $deleteTime->modify('- 1day');
        $deleteTime->setTime(
            AppConfig::CRON_MERGER_HOUR_RANGE_START,
            AppConfig::CRON_START_MINUTE
        );

        $this->rankingPositionHourRepository->dalete($deleteTime);
    }
}
