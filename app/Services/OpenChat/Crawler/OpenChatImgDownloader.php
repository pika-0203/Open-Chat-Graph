<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Crawler;

use App\Config\OpenChatCrawlerConfig;
use App\Services\Crawler\FileDownloader;

class OpenChatImgDownloader
{
    function __construct(
        private FileDownloader $fileDownloader
    ) {
    }

    /**
     * @return bool 成功した場合はtrue、 404の場合はfalse
     * 
     * @throws \RuntimeException
     */
    function storeOpenChatImg(string $openChatImgIdentifier, string $destPath, string $previewDestPath): bool
    {
        $url = OpenChatCrawlerConfig::LINE_IMG_URL . $openChatImgIdentifier;
        $previewUrl = $url . OpenChatCrawlerConfig::LINE_IMG_PREVIEW_PATH;

        $this->store(
            $url,
            $destPath,
            OpenChatCrawlerConfig::STORE_IMG_QUALITY,
        );

        $this->store(
            $previewUrl,
            $previewDestPath,
            80,
        );

        return true;
    }

    private function store(string $url, string $destPath, int $quality): void
    {
        try {
            $data = $this->fileDownloader->downloadFile($url, OpenChatCrawlerConfig::USER_AGENT);
            if ($data === false) {
                throw new \RuntimeException('画像のダウンロードに失敗: 404');
            }

            $source = imagecreatefromstring($data);
            if ($source === false) {
                throw new \RuntimeException('JPEGファイルの読み込み中にエラーが発生しました');
            }

            if (!imagewebp($source, $destPath, $quality)) {
                throw new \RuntimeException('WebPへの変換中にエラーが発生しました');
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
