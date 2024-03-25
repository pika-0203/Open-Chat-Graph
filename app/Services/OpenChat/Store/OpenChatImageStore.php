<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Store;

use App\Config\AppConfig;
use App\Services\OpenChat\Crawler\OpenChatImgDownloader;
use App\Models\Repositories\Log\LogRepositoryInterface;
use Shadow\DB;

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
    private function getImgPath(int $open_chat_id, string $fileName): array|false
    {
        if (in_array($fileName, AppConfig::DEFAULT_OPENCHAT_IMG_URL_HASH)) {
            return false;
        }

        return [
            'dest' => publicDir(getImgPath($open_chat_id, $fileName)),
            'previewDest' => publicDir(getImgPreviewPath($open_chat_id, $fileName)),
        ];
    }

    private function mkDir($open_chat_id): void
    {
        $subDir = '/' . filePathNumById($open_chat_id);
        mkdirIfNotExists(publicDir(AppConfig::OPENCHAT_IMG_PATH . $subDir));
        mkdirIfNotExists(publicDir(AppConfig::OPENCHAT_IMG_PREVIEW_PATH . $subDir));
    }

    /** @return string|false imgUrlHash */
    function downloadAndStoreOpenChatImage(int $open_chat_id, string $imgUrl): string|false
    {
        $imgUrlHash = base62Hash($imgUrl);

        $path = $this->getImgPath($open_chat_id, $imgUrlHash);
        if (!$path) {
            return $imgUrlHash;
        }

        $this->mkDir($open_chat_id);

        try {
            $this->imgDownloader->storeOpenChatImg($imgUrl, $path['dest'], $path['previewDest']);

            return $imgUrlHash;
        } catch (\RuntimeException $e) {
            // 再接続
            DB::$pdo = null;
            $this->logRepository->logOpenChatImageStoreError($imgUrl, $e->getMessage());

            return false;
        }
    }

    function deleteImage(int $open_chat_id, string $fileName): void
    {
        $path = $this->getImgPath($open_chat_id, $fileName);

        $path && array_map(fn (string $p) => file_exists($p) && unlink($p), $path);
    }
}
