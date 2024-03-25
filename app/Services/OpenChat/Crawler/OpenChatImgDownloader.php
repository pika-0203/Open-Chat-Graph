<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Crawler;

use App\Config\OpenChatCrawlerConfig;

class OpenChatImgDownloader
{
    /**
     * オープンチャットの画像をダウンロードする
     * 
     * @return bool 成功した場合はtrue、 404の場合はfalse
     * 
     * @throws \RuntimeException
     */
    function storeOpenChatImg(string $openChatImgIdentifier, string $destPath, string $previewDestPath): bool
    {
        /**
         *  @var string $url オープンチャットの画像取得URL
         *                   https://obs.line-scdn.net/{$profileImageObsHash}
         */
        $url = OpenChatCrawlerConfig::LINE_IMG_URL . $openChatImgIdentifier;

        /**
         *  @var string $previewUrl プレビュー画像取得URL
         *                          https://obs.line-scdn.net/{$profileImageObsHash}/preview
         */
        $previewUrl = $url . OpenChatCrawlerConfig::LINE_IMG_PREVIEW_PATH;

        $this->store(
            $url,
            $destPath,
            OpenChatCrawlerConfig::STORE_IMG_QUALITY,
        );

        $this->store(
            $previewUrl,
            $previewDestPath,
            OpenChatCrawlerConfig::STORE_IMG_QUALITY,
        );

        return true;
    }

    private function store(string $url, string $destPath, int $quality): void
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'prefix');

        try {
            $ch = curl_init($url);
            $fp = fopen($tempFilePath, 'w');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);

            $source = imagecreatefromjpeg($tempFilePath);
            if ($source === false) {
                unlink($tempFilePath);
                throw new \RuntimeException('JPEGファイルの読み込み中にエラーが発生しました');
            }

            if (!imagewebp($source, $tempFilePath, $quality)) {
                unlink($tempFilePath);
                throw new \RuntimeException('WebPへの変換中にエラーが発生しました');
            }

            chmod($tempFilePath, 0666);
            rename($tempFilePath, $destPath);
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
