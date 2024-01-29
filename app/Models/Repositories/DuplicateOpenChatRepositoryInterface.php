<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface DuplicateOpenChatRepositoryInterface
{
    public function getDuplicateOpenChatInfo(): array;
}
