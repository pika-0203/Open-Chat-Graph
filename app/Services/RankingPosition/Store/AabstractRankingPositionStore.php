<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Store;

use App\Config\AppConfig;
use App\Services\OpenChat\Dto\OpenChatDto;

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
        return [$fileTime, $data];
    }

    function deleteApiDtoStorageAll(): void
    {
        deleteStorageFileAll($this->filePath, true);
    }

    protected function getModifiedFileTime(string $fileName): string
    {
        $fileTime = new \DateTime('@' . filemtime($fileName));
        $fileTime->setTimeZone(new \DateTimeZone('Asia/Tokyo'));

        if ((int)$fileTime->format('i') < AppConfig::CRON_START_MINUTE) {
            $fileTime->modify('-1 hour');
        }

        $fileTime->setTime((int)$fileTime->format('H'), AppConfig::CRON_START_MINUTE);

        return $fileTime->format('Y-m-d H:i:s');
    }
}
