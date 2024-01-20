<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Registration;

use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Services\OpenChat\Store\OpenChatImageStore;
use App\Services\OpenChat\Dto\OpenChatDto;

class OpenChatFromApiRegistration
{
    function __construct(
        private OpenChatRepositoryInterface $openChatRepository,
        private OpenChatImageStore $openChatImageStore
    ) {
    }

    function registerOpenChatFromApi(OpenChatDto $apiDto): bool
    {
        $open_chat_id = $this->openChatRepository->addOpenChatFromDto($apiDto);

        if (!$this->openChatImageStore->downloadAndStoreOpenChatImage($apiDto->profileImageObsHash, $open_chat_id)) {
            // 画像のダウンロードに失敗した場合
            $this->openChatRepository->markAsNoImage($open_chat_id);
        }

        return true;
    }
}
