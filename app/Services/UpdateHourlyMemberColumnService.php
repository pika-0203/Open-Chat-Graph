<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\AppConfig;
use App\Models\Repositories\RankingPosition\HourMemberRankingUpdaterRepositoryInterface;
use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;

class UpdateHourlyMemberColumnService
{
    function __construct(
        private HourMemberRankingUpdaterRepositoryInterface $hourMemberRankingUpdaterRepository,
        private RankingPositionHourRepositoryInterface $rankingPositionHourRepository,
    ) {
    }

    function update()
    {
        $time = $this->rankingPositionHourRepository->getLastHour();
        if (!$time) return;

        $this->hourMemberRankingUpdaterRepository->updateHourRankingTable(new \DateTime($time));
        $this->updateStaticData($time);
    }

    private function updateStaticData(string $time)
    {
        $data = serialize(
            [
                'rankingUpdatedAt' => strtotime($time),
            ]
        );

        safeFileRewrite(AppConfig::TOP_RANKING_HOUR_INFO_FILE_PATH, $data);
    }
}
