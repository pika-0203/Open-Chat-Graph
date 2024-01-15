<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Store;

use App\Services\OpenChat\Crawler\OpenChatImgDownloader;
use App\Models\Repositories\LogRepositoryInterface;
use App\Config\OpenChatCrawlerConfig;

class OpenChatImageStore
{
    private OpenChatImgDownloader $imgDownloader;
    private LogRepositoryInterface $logRepository;

    function __construct(
        OpenChatImgDownloader $imgDownloader,
        LogRepositoryInterface $logRepository,
    ) {
        $this->imgDownloader = $imgDownloader;
        $this->logRepository = $logRepository;
    }

    function downloadAndStoreOpenChatImage(string $newOpenChatImgIdentifier, int $open_chat_id): bool
    {
        $imgPath = filePathNumById($open_chat_id);

        $destPath = OpenChatCrawlerConfig::STORE_IMG_DEST_PATH . "/{$imgPath}";
        if (file_exists("{$destPath}/{$newOpenChatImgIdentifier}.webp")) {
            // 同じ画像が存在する場合 (デフォルトのカバー画像)
            return true;
        }
        
        mkdirIfNotExists($destPath);

        $previewDestPath = $destPath . OpenChatCrawlerConfig::SOTRE_IMG_PREVIEW_DEST_PATH;
        mkdirIfNotExists($previewDestPath);

        try {
            $this->imgDownloader->storeOpenChatImg($newOpenChatImgIdentifier, $destPath);

            return true;
        } catch (\RuntimeException $e) {
            $this->logRepository->logOpenChatImageStoreError($newOpenChatImgIdentifier, $e->getMessage());

            return false;
        }
    }
}
