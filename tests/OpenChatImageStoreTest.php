<?php

declare(strict_types=1);

namespace Tests\Services\OpenChat\Crawler;

use App\Config\AppConfig;
use App\Services\OpenChat\Store\OpenChatImageStore;
use PHPUnit\Framework\TestCase;

class OpenChatImageStoreTest extends TestCase
{
    private OpenChatImageStore $downloader;

    public function setUp(): void
    {
        $this->downloader = app(OpenChatImageStore::class);
    }

    /**
     * @test
     */
    public function storeOpenChatImg(): void
    {
        $result = $this->downloader->downloadAndStoreOpenChatImage(1000000, '0htjheC_jkK0VVHDxFyOxUEmtKdmsubzJXKGQmI3UZcHB6LTtEO3M0dHUadCV-KmkVPipsISdPISArLz4');

        // テスト対象の実行

        // 検証
        $this->assertTrue($result);
    }
}
