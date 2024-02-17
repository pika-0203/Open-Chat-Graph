<?php

use App\Services\OpenChat\OpenChatApiDataParallelDownloader;
use App\Services\OpenChat\OpenChatApiDbMergerWithParallelDownloader;
use PHPUnit\Framework\TestCase;

class OpenChatApiDbMergerWithParallelDownloaderTest extends TestCase
{
    public function test()
    {
        /**
         * @var OpenChatApiDbMergerWithParallelDownloader $inst
         */
        $inst = app(OpenChatApiDbMergerWithParallelDownloader::class);

        OpenChatApiDataParallelDownloader::disableKillFlag();
        $result = $inst->fetchOpenChatApiRankingAll();

        var_dump($result);

        $this->assertIsInt(0);
    }
}
