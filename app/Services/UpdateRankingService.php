<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\AppConfig;
use App\Services\StaticData\StaticDataGenerator;
use App\Models\Repositories\Statistics\StatisticsRankingUpdaterRepositoryInterface;
use App\Models\Repositories\OpenChatListRepositoryInterface;

class UpdateRankingService
{
    function __construct(
        private StaticDataGenerator $staticDataGenerator,
        private StatisticsRankingUpdaterRepositoryInterface $rankingUpdater,
        private OpenChatListRepositoryInterface $openChatListRepository,
    ) {
    }

    /**
     * @param string $date Y-m-d
     * @return array `[$resultRowCount, $resultPastWeekRowCount]`
     */
    function update(string $date): array
    {
        $resultRowCount = $this->rankingUpdater->updateCreateDailyRankingTable($date);
        $resultPastWeekRowCount = $this->rankingUpdater->updateCreatePastWeekRankingTable($date);

        $this->updateStaticData($date);

        return [$resultRowCount, $resultPastWeekRowCount];
    }

    private function updateStaticData(string $date)
    {
        safeFileRewrite(AppConfig::DAILY_CRON_UPDATED_AT_DATE, $date);
        $this->staticDataGenerator->updateStaticData();
    }
}
