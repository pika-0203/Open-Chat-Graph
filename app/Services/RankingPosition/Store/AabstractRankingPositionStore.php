<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Store;

use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepositoryInterface;
use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourInsertDto;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

abstract class AabstractRankingPositionStore
{
    /**
     * @var OpenChatDto[] $apiDtoCache
     */
    protected array $apiDtoCache = [];
    protected string $filePath;

    function __construct(
        private OpenChatDataForUpdaterWithCacheRepositoryInterface $openChatDataWithCache,
    ) {
    }

    function addApiDto(OpenChatDto $apiDto)
    {
        $this->apiDtoCache[] = $apiDto;
    }

    function clearAllCacheDataAndSaveCurrentCategoryApiDataCache(string $category): void
    {
        $this->openChatDataWithCache->clearCache();

        saveSerializedArrayToFile(
            $this->filePath . "/{$category}.dat",
            $this->createInsertDtoArray($this->apiDtoCache),
            true
        );

        $this->apiDtoCache = [];
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

    /**
     * @return array{ 0: string, 1: RankingPositionHourInsertDto[] }
     */
    function getStorageData(string $category): array
    {
        $file = $this->filePath . "/{$category}.dat";
        $data = getUnserializedArrayFromFile($file, true);
        if (!$data) {
            throw new \RuntimeException('invalid ranking data file: ' . $file);
        }

        $fileTime = $this->getModifiedFileTime($file);
        return [$fileTime->format('Y-m-d H:i:s'), $data];
    }

    function deleteApiDtoStorageAll(): void
    {
        deleteStorageFileAll($this->filePath, true);
    }

    function getFileDateTime(string $category = '0'): \DateTime
    {
        return $this->getModifiedFileTime($this->filePath . "/{$category}.dat");
    }

    protected function getModifiedFileTime(string $fileName): \DateTime
    {
        return OpenChatServicesUtility::getModifiedCronTime(filemtime($fileName));
    }
}
