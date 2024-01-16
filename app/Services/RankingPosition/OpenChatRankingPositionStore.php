<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use Shadow\Kernel\Validator;

class OpenChatRankingPositionStore
{
    /**
     * @var array $apiDataCache [$category => [$apiData]]
     */
    private array $apiDataCache = [];

    function cacheApiData(string $category, array $apiData)
    {
        if (!isset($this->apiDataCache[$category])) {
            $this->apiDataCache[$category] = [];
        }

        $this->apiDataCache[$category][] = $apiData;
    }

    function saveClearApiDataCache(string $directory, bool $validate = false)
    {
        foreach ($this->apiDataCache as $category => $apiDataArray) {
            $emidArray = $this->createEmidArray($apiDataArray, $validate);
            saveSerializedArrayToFile("{$directory}/{$category}.dat", $emidArray, true);
        }

        $this->apiDataCache = [];
    }

    private function createEmidArray(array $apiDataArray, bool $validate): array
    {
        $result = [];
        foreach ($apiDataArray as $apiData) {
            $squares = $apiData['squaresByCategory'][0]['squares'];
            
            foreach ($squares as $square) {
                $result[] = $validate ? Validator::str($square['square']['emid'], e: \RuntimeException::class) : $square['square']['emid'];
            }
        }

        return $result;
    }
}
