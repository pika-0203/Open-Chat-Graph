<?php

declare(strict_types=1);

namespace Tests\Services\OpenChat\Crawler;

use App\Services\Crawler\FileDownloader;
use App\Services\OpenChat\Crawler\OpenChatImgDownloader;
use PHPUnit\Framework\TestCase;
use Shadow\File\FileValidator;
use Shadow\File\Image\GdImageFactory;
use Shadow\File\Image\ImageStore;
use Shadow\File\Image\ImageStoreInterface;

class OpenChatImgDownloaderTest extends TestCase
{
    private OpenChatImgDownloader $downloader;
    private FileDownloader $file;
    private FileValidator $validator;
    private GdImageFactory $image;
    private ImageStoreInterface $store;

    public function setUp(): void
    {
        $this->file = new FileDownloader();
        $this->validator = new FileValidator();
        $this->image = new GdImageFactory();
        $this->store = new ImageStore();

        $this->downloader = new OpenChatImgDownloader(
            $this->file,
            $this->validator,
            $this->image,
            $this->store,
        );
    }

    /**
     * @test
     */
    public function storeOpenChatImg(): void
    {
        // テストデータの準備
        $openChatImgIdentifier = '0hS3m23EutDBxQSxitH39zS24dUTIrOBUOLTMBL3JPBSh8KElNOSVLLSBIASUpKxhIaihCfHEcByV_fEM';

        // テスト対象の実行
        $result = $this->downloader->storeOpenChatImg($openChatImgIdentifier, 'test');

        // 検証
        $this->assertTrue($result);
    }
}
