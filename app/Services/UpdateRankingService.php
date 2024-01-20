<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\AppConfig;
use App\Services\StaticData\StaticTopPageDataGenerator;
use App\Models\Repositories\Statistics\StatisticsRankingUpdaterRepositoryInterface;
use App\Models\Repositories\OpenChatListRepositoryInterface;

class UpdateRankingService
{
    function __construct(
        private StaticTopPageDataGenerator $staticTopPageDataGenerator,
        private StatisticsRankingUpdaterRepositoryInterface $rankingUpdater,
        private OpenChatListRepositoryInterface $openChatListRepository,
    ) {
    }

    /**
     * @return array `[$resultRowCount, $resultPastWeekRowCount]`
     */
    function update(): array
    {
        $resultRowCount = $this->rankingUpdater->updateCreateDailyRankingTable();
        $resultPastWeekRowCount = $this->rankingUpdater->updateCreatePastWeekRankingTable();

        $this->updateStaticData($resultRowCount, $resultPastWeekRowCount);

        return [$resultRowCount, $resultPastWeekRowCount];
    }

    private function updateStaticData(int $resultRowCount, int $resultPastWeekRowCount)
    {
        $data = serialize(
            [
                'rankingUpdatedAt' => time(),
                'rankingRowCount' => $resultRowCount,
                'pastWeekRowCount' => $resultPastWeekRowCount,
                'recordCount' => $this->openChatListRepository->getRecordCount(),
            ]
        );

        safeFileRewrite(AppConfig::TOP_RANKING_INFO_FILE_PATH, $data);

        $this->staticTopPageDataGenerator->updateStaticTopPageData();
    }
}
