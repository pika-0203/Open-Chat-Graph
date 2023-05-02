<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface HandleNameRepositoryInterface
{
    /**
     * @return int handle_name_id
     */
    public function addHandleName(int $user_id, int $open_chat_id, string $name, string $img): int;

    /**
     * @return array `['id' => int, 'name' => string, 'img' => string]`
     */
    public function getHandleName(int $user_id, int $open_chat_id): array|false;
}
