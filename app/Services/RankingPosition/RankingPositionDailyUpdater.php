<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Config\AppConfig;
use App\Services\RankingPosition\Persistence\RankingPositionDailyPersistence;
use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class RankingPositionDailyUpdater
{
    private string $date;

    function __construct(
        private RankingPositionDailyPersistence $rankingPositionDailyPersistence,
        private StatisticsRepositoryInterface $statisticsRepository,
        private RankingPositionHourRepositoryInterface $rankingPositionHourRepository,
        private UpdateOpenChatRepositoryInterface $updateRepository,
    ) {
        $this->date = OpenChatServicesUtility::getCronModifiedStatsMemberDate();
    }

    function updateYesterdayDailyDb()
    {
        $this->persistMemberStatsFromRankingPositionDb();
        $this->rankingPositionDailyPersistence->persistHourToDaily();
    }

    private function persistMemberStatsFromRankingPositionDb(): void
    {
        $todayLastTime = new \DateTime($this->date);
        $todayLastTime->setTime(
            AppConfig::CRON_MERGER_HOUR_RANGE_START,
            AppConfig::CRON_START_MINUTE
        );

        $data = $this->rankingPositionHourRepository->getDailyMemberStats($todayLastTime);
        $ocDbIdArray = $this->updateRepository->getOpenChatIdAll();

        $filteredData = array_filter($data, fn ($stats) => in_array($stats['open_chat_id'], $ocDbIdArray));
        $this->statisticsRepository->insertMember($filteredData);
    }
}
