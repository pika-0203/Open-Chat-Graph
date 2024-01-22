<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Services\OpenChat\Dto\OpenChatDto;

interface DuplicateOpenChatRepositoryInterface
{
    public function getDuplicateOpenChatInfo(): array;

    public function findDuplicateOpenChat(OpenChatDto $dto): int|false;
}
