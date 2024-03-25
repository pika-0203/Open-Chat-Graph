<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater;

use App\Config\AppConfig;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Store\OpenChatImageStore;

class OpenChatImageStoreUpdater
{
    function __construct(
        private OpenChatImageStore $openChatImageStore,
        private UpdateOpenChatRepositoryInterface $updateOpenChatRepository,
    ) {
    }

    function updateImage(
        int $open_chat_id,
        string $newImgUrl,
        string $oldLocalImgUrl = AppConfig::ADD_OPEN_CHAT_DEFAULT_OPENCHAT_IMG_URL_HASH
    ): bool {
        $local_img_url = $this->openChatImageStore->downloadAndStoreOpenChatImage($open_chat_id, $newImgUrl);
        if (!$local_img_url) return false;

        $this->updateOpenChatRepository->updateLocalImgUrl($open_chat_id, $local_img_url);
        if (!in_array($local_img_url, AppConfig::DEFAULT_OPENCHAT_IMG_URL_HASH))
            $this->openChatImageStore->deleteImage($open_chat_id, $oldLocalImgUrl);

        return true;
    }
}
