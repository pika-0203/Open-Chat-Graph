<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater\Process;

use App\Services\OpenChat\Store\OpenChatImageStore;
use App\Services\OpenChat\Dto\OpenChatUpdaterDto;
use App\Services\OpenChat\Store\OpenChatImageDeleter;
use App\Services\OpenChat\Dto\ArchiveFlagsDto;

class OpenChatUpdaterImageProcess
{
    private OpenChatImageStore $openChatImageStore;
    private OpenChatImageDeleter $openChatImageDeleter;

    function __construct(
        OpenChatImageStore $openChatImageStore,
        OpenChatImageDeleter $openChatImageDeleter,
    ) {
        $this->openChatImageStore = $openChatImageStore;
        $this->openChatImageDeleter = $openChatImageDeleter;
    }

    function processImageBeforeUpdate(OpenChatUpdaterDto $updaterDto)
    {
        if (!isset($updaterDto->profileImageObsHash)) {
            return;
        }

        $updaterDto->profileImageObsHash = $this->downloadAndStoreOpenChatImage(
            $updaterDto->profileImageObsHash,
            $updaterDto->open_chat_id
        );
    }

    function processImageAfterUpdate(OpenChatUpdaterDto $updaterDto, ArchiveFlagsDto|false $archiveFlagsDto)
    {
        $isNoEmidUpdateImg = !$updaterDto->hasEmid && $archiveFlagsDto && $archiveFlagsDto->update_img;
        
        if ($updaterDto->delete_flag || $isNoEmidUpdateImg) {
            $this->openChatImageDeleter->deleteImage($updaterDto->open_chat_id, $updaterDto->db_img_url);
        }
    }

    private function downloadAndStoreOpenChatImage(string $profileImageObsHash, int $open_chat_id): string
    {
        $imageStoreResult = $this->openChatImageStore->downloadAndStoreOpenChatImage($profileImageObsHash, $open_chat_id);
        if (!$imageStoreResult) {
            return 'noimage';
        }

        return $profileImageObsHash;
    }
}
