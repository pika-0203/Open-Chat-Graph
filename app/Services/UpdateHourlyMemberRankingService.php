<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\AppConfig;
use App\Models\Repositories\RankingPosition\HourMemberRankingUpdaterRepositoryInterface;
use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Services\StaticData\StaticTopPageDataGenerator;

class UpdateHourlyMemberRankingService
{
    function __construct(
        private StaticTopPageDataGenerator $staticTopPageDataGenerator,
        private HourMemberRankingUpdaterRepositoryInterface $hourMemberRankingUpdaterRepository,
        private RankingPositionHourRepositoryInterface $rankingPositionHourRepository,
        private StatisticsRepositoryInterface $statisticsRepository,
    ) {
    }

    function update()
    {
        $time = $this->rankingPositionHourRepository->getLastHour();
        if (!$time) return;


        $dateTime = new \DateTime($time);

        $filters = getUnserializedArrayFromFile(AppConfig::OPEN_CHAT_HOUR_FILTER_ID_DIR, true);
        if (!$filters) {
            $filters = $this->statisticsRepository->getHourMemberChangeWithinLastWeekArray($dateTime->format('Y-m-d'));
        }

        $this->hourMemberRankingUpdaterRepository->updateHourRankingTable(new \DateTime($time), $filters);

        $this->updateStaticData((new \DateTime())->format('Y-m-d H:i:s'));

        saveSerializedArrayToFile(
            AppConfig::OPEN_CHAT_HOUR_FILTER_ID_DIR,
            $this->statisticsRepository->getHourMemberChangeWithinLastWeekArray($dateTime->format('Y-m-d')),
            true
        );
    }

    private function updateStaticData(string $time)
    {
        $data = serialize(
            [
                'rankingUpdatedAt' => strtotime($time),
            ]
        );

        safeFileRewrite(AppConfig::TOP_RANKING_HOUR_INFO_FILE_PATH, $data);

        $this->staticTopPageDataGenerator->updateStaticTopPageData();
    }
}
