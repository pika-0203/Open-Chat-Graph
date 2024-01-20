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
        foreach (AppConfig::OPEN_CHAT_CATEGORY as $category) {
            $this->rankingPositionHourRepository->insertRisingHourFromDtoArray(
                ...$this->risingPositionStore->getStorageData((string)$category)
            );

            $this->rankingPositionHourRepository->insertRankingHourFromDtoArray(
                ...$this->rankingPositionStore->getStorageData((string)$category)
            );
        }
    }
}
