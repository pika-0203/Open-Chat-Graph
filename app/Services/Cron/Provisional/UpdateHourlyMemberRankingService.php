<?php

declare(strict_types=1);

namespace App\Services\Cron\Provisional;

use App\Config\AppConfig;
use App\Models\Repositories\RankingPosition\HourMemberRankingUpdaterRepositoryInterface;
use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Services\Recommend\StaticData\RecommendStaticDataGenerator;
use App\Services\StaticData\StaticDataGenerator;

class UpdateHourlyMemberRankingService
{
    function __construct(
        private StaticDataGenerator $staticDataGenerator,
        private RecommendStaticDataGenerator $recommendStaticDataGenerator,
        private HourMemberRankingUpdaterRepositoryInterface $hourMemberRankingUpdaterRepository,
        private RankingPositionHourRepositoryInterface $rankingPositionHourRepository,
        private StatisticsRepositoryInterface $statisticsRepository,
    ) {
    }

    function update()
    {
        $time = $this->rankingPositionHourRepository->getLastHour();
        if (!$time) return;

        $this->hourMemberRankingUpdaterRepository->updateHourRankingTable(
            new \DateTime($time),
            $this->getCachedFilters($time)
        );

        $this->updateStaticData($time);
        $this->saveNextFiltersCache($time);
    }

    private function getCachedFilters(string $time)
    {
        $filters = getUnserializedFile(AppConfig::$OPEN_CHAT_HOUR_FILTER_ID_DIR, true);
        return $filters
            ? $filters
            : $this->statisticsRepository->getHourMemberChangeWithinLastWeekArray((new \DateTime($time))->format('Y-m-d'));
    }

    private function saveNextFiltersCache(string $time)
    {
        saveSerializedFile(
            AppConfig::$OPEN_CHAT_HOUR_FILTER_ID_DIR,
            $this->statisticsRepository->getHourMemberChangeWithinLastWeekArray((new \DateTime($time))->format('Y-m-d')),
            true
        );
    }

    private function updateStaticData(string $time)
    {
        safeFileRewrite(AppConfig::$HOURLY_CRON_UPDATED_AT_DATETIME, $time);

        //$this->staticDataGenerator->updateStaticData();
        safeFileRewrite(AppConfig::$HOURLY_REAL_UPDATED_AT_DATETIME, (new \DateTime)->format('Y-m-d H:i:s'));
        //$this->recommendStaticDataGenerator->updateStaticData();
    }
}
