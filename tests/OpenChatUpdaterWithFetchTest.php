<?php

declare(strict_types=1);

namespace Shadow\File;

use App\Services\OpenChat\Updater\OpenChatUpdaterWithFetch;
use App\Services\OpenChat\Crawler\OpenChatCrawler;
use PHPUnit\Framework\TestCase;

class OpenChatUpdaterWithFetchTest extends TestCase
{
    public function test(): void
    {
        /**
         * @var OpenChatUpdaterWithFetch $updater
         */
        $updater = app(OpenChatUpdaterWithFetch::class);


        $result = $updater->updateOpenChat(1, app(OpenChatCrawler::class));
        $this->assertTrue($result);
    }
}
