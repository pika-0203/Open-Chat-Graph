<?php

use PHPUnit\Framework\TestCase;
use App\Services\OpenChat\Crawler\OpenChatApiRankingDownloader;
use App\Services\OpenChat\RankingPosition\OpenChatRankingPositionStore;
use App\Config\AppConfig;
use App\Services\OpenChat\Crawler\OpenChatApiRankingDownloaderProcess;
use App\Services\OpenChat\Crawler\OpenChatApiRisingDownloaderProcess;

class OpenChatRankingPositionStoreTest extends TestCase
{
    public function testfetchSaveOpenChatRankingApiData()
    {
        /**
         * @var OpenChatRankingPositionStore $openChatRankingPositionStore
         */
        $openChatRankingPositionStore = app(OpenChatRankingPositionStore::class);

        /**
         * @var OpenChatApiRankingDownloader $openChatApiRankingDataDownloader
         */
        $openChatApiRankingDownloaderProcess = app(OpenChatApiRankingDownloaderProcess::class);
        $openChatApiRankingDataDownloader = app(OpenChatApiRankingDownloader::class, compact('openChatApiRankingDownloaderProcess'));

        $res = $openChatApiRankingDataDownloader->fetchOpenChatApiRankingAll(1, 21, function ($apiData, $category) use ($openChatRankingPositionStore) {
            var_dump($category);
            $openChatRankingPositionStore->cacheApiData($category, $apiData);
        });

        $res = $openChatApiRankingDataDownloader->fetchOpenChatApiRankingAll(1, 25, function ($apiData, $category) use ($openChatRankingPositionStore) {
            var_dump($category);
            $openChatRankingPositionStore->cacheApiData($category, $apiData);
        });

        $openChatRankingPositionStore->saveClearApiDataCache(AppConfig::OPEN_CHAT_RANKING_POSITION_DIR);

        var_dump($res);

        $this->testShowPositionData(AppConfig::OPEN_CHAT_RANKING_POSITION_DIR . '/27.dat');
        $this->testShowPositionData(AppConfig::OPEN_CHAT_RANKING_POSITION_DIR . '/0.dat');

        $this->assertTrue($res > 0);
    }

    public function testfetchSaveOpenChatRankingApiDataRising()
    {
        /**
         * @var OpenChatRankingPositionStore $openChatRankingPositionStore
         */
        $openChatRankingPositionStore = app(OpenChatRankingPositionStore::class);

        /**
         * @var OpenChatApiRankingDownloader $openChatApiRankingDataDownloader
         */
        $openChatApiRankingDownloaderProcess = app(OpenChatApiRisingDownloaderProcess::class);
        $openChatApiRankingDataDownloader = app(OpenChatApiRankingDownloader::class, compact('openChatApiRankingDownloaderProcess'));

        $res = $openChatApiRankingDataDownloader->fetchOpenChatApiRankingAll(1, 21, function ($apiData, $category) use ($openChatRankingPositionStore) {
            var_dump($category);
            $openChatRankingPositionStore->cacheApiData($category, $apiData);
        });

        $res = $openChatApiRankingDataDownloader->fetchOpenChatApiRankingAll(1, 25, function ($apiData, $category) use ($openChatRankingPositionStore) {
            var_dump($category);
            $openChatRankingPositionStore->cacheApiData($category, $apiData);
        });

        $openChatRankingPositionStore->saveClearApiDataCache(AppConfig::OPEN_CHAT_RISING_POSITION_DIR);

        var_dump($res);

        $this->testShowPositionData(AppConfig::OPEN_CHAT_RISING_POSITION_DIR . '/27.dat');
        $this->testShowPositionData(AppConfig::OPEN_CHAT_RISING_POSITION_DIR . '/0.dat');

        $this->assertTrue($res > 0);
    }

    private function testShowPositionData(string $fileName)
    {
        $data = getUnserializedArrayFromFile($fileName, true);
        var_dump($data);
        $this->assertIsArray($data);
    }
}
