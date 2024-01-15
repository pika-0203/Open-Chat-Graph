<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Config\AppConfig;
use App\Services\OpenChat\Crawler\OpenChatApiRankingDownloader;
use App\Services\OpenChat\Crawler\OpenChatApiRisingDownloaderProcess;
use App\Services\RankingPosition\OpenChatRankingPositionStore;

class RisingPositionCrawling
{
    private OpenChatRankingPositionStore $openChatRankingPositionStore;
    private OpenChatApiRankingDownloader $openChatApiRisingDataDownloader;

    function __construct(
        OpenChatRankingPositionStore $openChatRankingPositionStore,
        OpenChatApiRisingDownloaderProcess $openChatApiRisingDownloaderProcess
    ) {
        $this->openChatRankingPositionStore = $openChatRankingPositionStore;
        $this->openChatApiRisingDataDownloader = app(
            OpenChatApiRankingDownloader::class,
            ['openChatApiRankingDownloaderProcess' => $openChatApiRisingDownloaderProcess]
        );

        deleteStorageFileAll(AppConfig::OPEN_CHAT_RISING_POSITION_DIR, true);
    }

    function risingPositionCrawling()
    {
        $this->openChatApiRisingDataDownloader->fetchOpenChatApiRankingAll(100, 1, function ($apiData, $category) {
            $this->openChatRankingPositionStore->cacheApiData($category, $apiData);
        });

        $this->openChatRankingPositionStore->saveClearApiDataCache(AppConfig::OPEN_CHAT_RISING_POSITION_DIR, true);
    }
}
