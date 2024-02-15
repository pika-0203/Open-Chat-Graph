<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Persistence;

use App\Config\AppConfig;
use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;
use App\Services\RankingPosition\Store\RankingPositionStore;
use App\Services\RankingPosition\Store\RisingPositionStore;

class RankingPositionHourPersistence
{
    function __construct(
        private RankingPositionHourRepositoryInterface $rankingPositionHourRepository,
        private RisingPositionStore $risingPositionStore,
        private RankingPositionStore $rankingPositionStore
    ) {
    }

    function persistStorageFileToDb(): void
    {
        $fileTime = $this->persist();

        $this->rankingPositionHourRepository->insertTotalCount($fileTime);
        addCronLog("HourPersistence TotalCountInsert: {$fileTime}");

        $deleteTime = new \DateTime($fileTime);
        $deleteTime->modify('- 1day');
        $this->rankingPositionHourRepository->dalete($deleteTime);

        $deleteTimeStr = $deleteTime->format('Y-m-d H:i:s');
        addCronLog("HourPersistence Delete: {$deleteTimeStr}");
    }

    private function persist(): string
    {
        $fileTime = '';
        foreach (AppConfig::OPEN_CHAT_CATEGORY as $key => $category) {
            [$risingFileTime, $risingInsertDtoArray] = $this->risingPositionStore->getStorageData((string)$category);
            $this->rankingPositionHourRepository->insertRisingHourFromDtoArray($risingFileTime, $risingInsertDtoArray);
            $this->rankingPositionHourRepository->insertHourMemberFromDtoArray($risingFileTime, $risingInsertDtoArray);
            unset($risingInsertDtoArray);
            addCronLog("HourPersistence Rising: {$key}");

            [$rankingFileTime, $rankingInsertDtoArray] = $this->rankingPositionStore->getStorageData((string)$category);
            $this->rankingPositionHourRepository->insertRankingHourFromDtoArray($rankingFileTime, $rankingInsertDtoArray);
            $this->rankingPositionHourRepository->insertHourMemberFromDtoArray($rankingFileTime, $rankingInsertDtoArray);
            unset($rankingInsertDtoArray);
            addCronLog("HourPersistence Ranking: {$key}");

            $fileTime = $rankingFileTime;
        }

        return $fileTime;
    }
}
