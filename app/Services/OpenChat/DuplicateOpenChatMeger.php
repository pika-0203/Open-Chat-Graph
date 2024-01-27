<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Models\Repositories\DeleteOpenChatRepositoryInterface;
use App\Models\Repositories\DuplicateOpenChatRepositoryInterface;

class DuplicateOpenChatMeger
{
    function __construct(
        private DeleteOpenChatRepositoryInterface $DeleteOpenChatRepository,
        private DuplicateOpenChatRepositoryInterface $duplicateOpenChatRepository,
    ) {
    }

    function mergeDuplicateOpenChat(): array
    {
        $duplicateOpenChat = $this->duplicateOpenChatRepository->getDuplicateOpenChatInfo();

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
            $this->DeleteOpenChatRepository->deleteDuplicatedOpenChat($duplicated_id, $open_chat_id);
        }
    }
}
