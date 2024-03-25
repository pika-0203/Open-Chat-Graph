<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Store;

use App\Config\AppConfig;
use App\Services\OpenChat\Crawler\OpenChatImgDownloader;
use App\Models\Repositories\Log\LogRepositoryInterface;

class OpenChatImageStore
{
    function __construct(
        private OpenChatImgDownloader $imgDownloader,
        private LogRepositoryInterface $logRepository,
    ) {
    }

    /**
     * @return array{ dest:string,previewDest:string }|false
     */
    private function getImgPath(int $open_chat_id, string $imgUrl): array|false
    {
        if (in_array($imgUrl, AppConfig::DEFAULT_OPENCHAT_IMG_URL)) {
            return false;
        }

        return [
            'dest' => publicDir(getImgPath($open_chat_id, $imgUrl)),
            'previewDest' => publicDir(getImgPreviewPath($open_chat_id, $imgUrl)),
        ];
    }

    private function mkDir($open_chat_id) :void
    {
        $subDir = '/' . filePathNumById($open_chat_id);
        mkdirIfNotExists(publicDir(AppConfig::OPENCHAT_IMG_PATH . $subDir));
        mkdirIfNotExists(publicDir(AppConfig::OPENCHAT_IMG_PREVIEW_PATH . $subDir));
    }

    function downloadAndStoreOpenChatImage(int $open_chat_id, string $imgUrl): bool
    {
        $path = $this->getImgPath($open_chat_id, $imgUrl);
        if (!$path) {
            return true;
        }

        $this->mkDir($open_chat_id);

        try {
            $this->imgDownloader->storeOpenChatImg($imgUrl, $path['dest'], $path['previewDest']);

            return true;
        } catch (\RuntimeException $e) {
            $this->logRepository->logOpenChatImageStoreError($imgUrl, $e->getMessage());

            return false;
        }
    }

    function deleteImage(int $open_chat_id, string $imgUrl): void
    {
        $path = $this->getImgPath($open_chat_id, $imgUrl);

        $path && array_map(fn (string $p) => file_exists($p) && unlink($p), $path);
    }
}
