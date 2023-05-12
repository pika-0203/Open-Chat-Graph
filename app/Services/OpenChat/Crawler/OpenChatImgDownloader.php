<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Crawler;

use App\Config\AppConfig;
use App\Config\OpenChatCrawlerConfig;
use App\Services\Crawler\FileDownloader;
use App\Services\Crawler\TraitUserAgent;
use Shadow\File\FileValidatorInterface;
use Shadow\File\Image\GdImageFactoryInterface;
use Shadow\File\Image\ImageStoreInterface;
use Shadow\Exceptions\ValidationException;

class OpenChatImgDownloader
{
    use TraitUserAgent;

    private FileDownloader $file;
    private FileValidatorInterface $validator;
    private GdImageFactoryInterface $image;
    private ImageStoreInterface $store;

    function __construct(
        FileDownloader $file,
        FileValidatorInterface $validator,
        GdImageFactoryInterface $image,
        ImageStoreInterface $store
    ) {
        $this->file = $file;
        $this->validator = $validator;
        $this->image = $image;
        $this->store = $store;
    }

    /**
     * オープンチャットの画像をダウンロードする
     * 
     * @return bool 成功した場合はtrue、 404の場合はfalse
     * 
     * @throws \RuntimeException
     */
    function storeOpenChatImg(string $openChatImgIdentifier): bool
    {
        $url = OpenChatCrawlerConfig::LINE_IMG_URL . $openChatImgIdentifier;

        $openChatImg = $this->download($url);
        if (!$openChatImg) {
            return false;
        }

        $openChatPreviewImg = $this->download($url . OpenChatCrawlerConfig::LINE_IMG_PREVIEW_PATH);
        if (!$openChatPreviewImg) {
            throw new \RuntimeException('Preview image not found: ' . $openChatImgIdentifier);
        }

        $this->store(
            $openChatImg,
            $openChatImgIdentifier,
            OpenChatCrawlerConfig::SOTRE_IMG_DEST_PATH,
            OpenChatCrawlerConfig::STORE_IMG_QUALITY,
        );

        $this->store(
            $openChatPreviewImg,
            $openChatImgIdentifier . AppConfig::LINE_IMG_PREVIEW_SUFFIX,
            OpenChatCrawlerConfig::SOTRE_IMG_PREVIEW_DEST_PATH,
            OpenChatCrawlerConfig::STORE_IMG_QUALITY,
        );

        return true;
    }

    private function download(string $url): string|false
    {
        $ua = 'Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36 (compatible; OpenChatStatsbot; +https://github.com/pika-0203/Open-Chat-Graph)';

        $downloadData = $this->file->downloadFile($url, $ua);
        if (!$downloadData) {
            return false;
        }

        try {
            $this->validator->validate($downloadData, DEFAULT_MAX_FILE_SIZE, OpenChatCrawlerConfig::IMG_MIME_TYPE);
            return $downloadData;
        } catch (ValidationException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    private function store(
        string $downloadData,
        string $fileName,
        string $destPath,
        int $quality
    ) {
        $openChatImg = $this->image->createGdImage($downloadData);
        $this->store->storeImageFromGdImage($openChatImg, $destPath, $fileName, quality: $quality);
    }
}
