<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Persistence;

use App\Config\AppConfig;
use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepositoryInterface;
use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourInsertDto;
use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;
use App\Services\RankingPosition\Store\RankingPositionStore;
use App\Services\RankingPosition\Store\RisingPositionStore;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class RankingPositionHourPersistence
{
    private string $cronStartTime;

    function __construct(
        private RankingPositionHourRepositoryInterface $rankingPositionHourRepository,
        private OpenChatDataForUpdaterWithCacheRepositoryInterface $openChatDataWithCache,
        private RisingPositionStore $risingPositionStore,
        private RankingPositionStore $rankingPositionStore
    ) {
        $this->cronStartTime = OpenChatServicesUtility::getModifiedCronTime('now')->format('Y-m-d H:i:s');
    }

    function persistStorageFileToDb(): void
    {
        $fileTime = '';
        foreach (AppConfig::OPEN_CHAT_CATEGORY as $category) {
            [$risingFileTime, $risingData] = $this->risingPositionStore->getStorageData((string)$category);
            $risingInsertDtoArray = $this->createInsertDtoArray($risingData);
            $this->rankingPositionHourRepository->insertRisingHourFromDtoArray($risingFileTime, $risingInsertDtoArray);
            $this->rankingPositionHourRepository->insertHourMemberFromDtoArray($risingFileTime, $risingInsertDtoArray);

            [$rankingFileTime, $rankingData] = $this->rankingPositionStore->getStorageData((string)$category);
            $rankingInsertDtoArray = $this->createInsertDtoArray($rankingData);
            $this->rankingPositionHourRepository->insertRankingHourFromDtoArray($rankingFileTime, $rankingInsertDtoArray);
            $this->rankingPositionHourRepository->insertHourMemberFromDtoArray($rankingFileTime, $rankingInsertDtoArray);

            $fileTime = $rankingFileTime;
        }

        $this->rankingPositionHourRepository->insertTotalCount($fileTime);

        $deleteTime = new \DateTime($this->cronStartTime);
        $deleteTime->modify('- 1day');
        $this->rankingPositionHourRepository->dalete($deleteTime);
    }

    /**
     * @param OpenChatDto[] $data 
     * @return RankingPositionHourInsertDto[]
     */
    private function createInsertDtoArray(array $data): array
    {
        $result = [];
        foreach ($data as $key => $dto) {
            $id = $this->openChatDataWithCache->getOpenChatIdByEmid($dto->emid);
            if (!$id) {
                continue;
            }

            $result[] = new RankingPositionHourInsertDto(
                $id,
                $key + 1,
                $dto->category ?? 0,
                $dto->memberCount
            );
        }

        return $result;
    }
}
