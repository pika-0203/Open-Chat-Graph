<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Store;

use App\Config\OpenChatCrawlerConfig;
use App\Config\AppConfig;

class OpenChatImageDeleter
{
    function deleteImage(int $open_chat_id, string $imgUrl): void
    {
        if(in_array($imgUrl, AppConfig::DEFAULT_OPENCHAT_IMG_URL)) {
            return;
        }

        $imgRootDir = OpenChatCrawlerConfig::STORE_IMG_DEST_PATH;
        $idPath = filePathNumById($open_chat_id);
        $imgDir = "{$imgRootDir}/{$idPath}";

        $this->deleteFileIfExists("{$imgDir}/{$imgUrl}.webp");
        $this->deleteFileIfExists("{$imgDir}/preview/{$imgUrl}_p.webp");
    }

    private function deleteFileIfExists(string $filePath): void
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
