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
        // テストデータの準備
        $openChatImgIdentifier = '0h6tJf8QWOaVt3H0eLAsEWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnOQ';

        foreach(AppConfig::DEFAULT_OPENCHAT_IMG_URL as $url) {
            $result = $this->downloader->downloadAndStoreOpenChatImage(1, $url);
        }

        // テスト対象の実行

        // 検証
        $this->assertTrue($result);
    }
}
