<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Services\OpenChat\Store\OpenChatImageDeleter;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;

class DuplicateOpenChatMeger
{
    function __construct(
        private UpdateOpenChatRepositoryInterface $updateOpenChatRepository,
        private OpenChatImageDeleter $openChatImageDeleter
    ) {
    }

    function mergeDuplicateOpenChat(): array
    {
        $duplicateOpenChat = $this->updateOpenChatRepository->getDuplicateOpenChatInfo();

        foreach ($duplicateOpenChat as $oc) {
            $this->processMergeDuplicateOpenChat($oc);
        }

        return $duplicateOpenChat;
    }

    function processMergeDuplicateOpenChat(array $openChat): void
    {
        /**
         * @var array $idArray
         * @var string $imgUrl
         */
        ['id' => $idArray, 'img_url' => $imgUrl] = $openChat;

        // open_chat_id が一番古いものを残す
        $open_chat_id = min($idArray);
        $duplicateIdArray = array_filter($idArray, fn ($id) => $id !== $open_chat_id);

        foreach ($duplicateIdArray as $duplicated_id) {
            $this->updateOpenChatRepository->deleteDuplicateOpenChat($duplicated_id, $open_chat_id);

            // 違うディレクトリに同じ画像がある場合は削除する
            if (filePathNumById($duplicated_id) !== filePathNumById($open_chat_id)) {
                $this->openChatImageDeleter->deleteImage($duplicated_id, $imgUrl);
            }
        }
    }
}
