<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Store;

use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

abstract class AabstractRankingPositionStore
{
    /**
     * @var array $apiDtoCache [$apiDto]
     */
    protected array $apiDtoCache = [];
    protected string $filePath;

    function addApiDto(OpenChatDto $apiDto)
    {
        $this->apiDtoCache[] = $apiDto;
    }

    function saveClearCurrentCategoryApiDataCache(string $category): void
    {
        saveSerializedArrayToFile(
            $this->filePath . "/{$category}.dat",
            $this->apiDtoCache,
            true
        );

        $this->apiDtoCache = [];
    }

    /**
     * @return array `[$fileTime, $data]` string $fileTime "Y-m-d H:i:s", array $data [OpenChatDto]
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
