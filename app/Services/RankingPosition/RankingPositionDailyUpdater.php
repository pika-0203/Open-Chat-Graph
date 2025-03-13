<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Models\RankingPositionDB\RankingPositionDB;
use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Services\RankingPosition\Persistence\RankingPositionDailyPersistence;
use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class RankingPositionDailyUpdater
{
    private string $date;

    function __construct(
        private RankingPositionDailyPersistence $rankingPositionDailyPersistence,
        private StatisticsRepositoryInterface $statisticsRepository,
        private RankingPositionHourRepositoryInterface $rankingPositionHourRepository,
        private OpenChatRepositoryInterface $openChatRepository,
    ) {
        $this->date = OpenChatServicesUtility::getCronModifiedStatsMemberDate();
    }

    function updateYesterdayDailyDb()
    {
        $this->rankingPositionDailyPersistence->persistHourToDaily();
        $this->persistMemberStatsFromRankingPositionDb();
    }

    private function persistMemberStatsFromRankingPositionDb(): void
    {
        RankingPositionDB::$pdo = null;
        $data = $this->rankingPositionHourRepository->getDailyMemberStats(new \DateTime($this->date));

        RankingPositionDB::$pdo = null;
        $ocDbIdArray = $this->openChatRepository->getOpenChatIdAll();

        $filteredData = array_filter($data, fn ($stats) => in_array($stats['open_chat_id'], $ocDbIdArray));
        unset($ocDbIdArray);
        
        $this->statisticsRepository->insertMember($filteredData);
    }
}
