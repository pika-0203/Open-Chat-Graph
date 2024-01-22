<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface DeleteOpenChatRepositoryInterface
{
    public function deleteOpenChat(int $open_chat_id): bool;

    public function deleteDuplicatedOpenChat(int $duplicated_id, int $open_chat_id): void;
}
