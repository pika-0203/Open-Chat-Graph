<?php

declare(strict_types=1);

namespace Shadow\File;

use PHPUnit\Framework\TestCase;
use App\Services\OpenChat\OpenChatCrawlingFromPage;

class OpenChatCrawlingFromPageTest extends TestCase
{
    public function testcaluclatemaxExecuteNum(): void
    {
        /**
         * @var OpenChatCrawlingFromPage $updater
         */
        $updater = app(OpenChatCrawlingFromPage::class);
        $result = $updater->caluclatemaxExecuteNum();

        debug($result);
        $this->assertIsInt($result);
    }

    public function testopenChatCrawlingFromPage(): void
    {
        /**
         * @var OpenChatCrawlingFromPage $updater
         */
        $updater = app(OpenChatCrawlingFromPage::class);
        $updater->openChatCrawling(10);

        $this->assertTrue(true);
    }
}
