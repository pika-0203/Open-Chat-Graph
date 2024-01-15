<?php

use PHPUnit\Framework\TestCase;
use App\Services\OpenChat\Crawler\OpenChatApiRankingDownloader;

class OpenChatApiRankingDownloaderTest extends TestCase
{
    public function testfetchSaveOpenChatRankingApiData()
    {
        /**
         * @var OpenChatApiRankingDownloader $openChatApiRankingDataDownloader
         */
        $openChatApiRankingDataDownloader = app(OpenChatApiRankingDownloader::class);

        $res = $openChatApiRankingDataDownloader->fetchOpenChatApiRankingAll(1, 21, function ($apiData) {
            var_dump($apiData);
        });

        var_dump($res);

        $this->assertTrue($res > 0);
    }

    public function testcountMaxExecuteNum()
    {
        /**
         * @var OpenChatApiRankingDownloader $openChatApiRankingDataDownloader
         */
        $openChatApiRankingDataDownloader = app(OpenChatApiRankingDownloader::class);

        $res = $openChatApiRankingDataDownloader->countMaxExecuteNum(3);

        var_dump($res);

        $this->assertTrue($res > 0);
    }
}
