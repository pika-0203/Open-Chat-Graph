<?php

declare(strict_types=1);

namespace App\Services;

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
        $filters = getUnserializedFile(getStorageFilePath(AppConfig::STORAGE_FILES['openChatHourFilterId']));
        return $filters
            ? $filters
            : $this->statisticsRepository->getHourMemberChangeWithinLastWeekArray((new \DateTime($time))->format('Y-m-d'));
    }

    private function saveNextFiltersCache(string $time)
    {
        saveSerializedFile(
            getStorageFilePath(AppConfig::STORAGE_FILES['openChatHourFilterId']),
            $this->statisticsRepository->getHourMemberChangeWithinLastWeekArray((new \DateTime($time))->format('Y-m-d')),
        );
    }

    private function updateStaticData(string $time)
    {
        safeFileRewrite(getStorageFilePath(AppConfig::STORAGE_FILES['hourlyCronUpdatedAtDatetime']), $time);
        $this->staticDataGenerator->updateStaticData();
        $this->recommendStaticDataGenerator->updateStaticData();
    }
}
