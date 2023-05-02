<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface OpenChatRepositoryInterface
{
    /**
     * @return array|false
     * `['id' => int, 'name' => string, 'url' => string, 'img_url' => string, 'descripton' => string, 'member' => int, 'updated_at' => string, 'is_alive' => int]`
     */
    public function getOpenChatById(int $id): array|false;

    public function getOpenChatIdByImgUrl(string $img_url): int|false;

    public function getOpenChatIdByUrl(string $url): int|false;

    /**
     * @return int id
     */
    public function addOpenChat(
        string $name,
        string $url,
        string $img_url,
        string $description,
        int $member,
    ): int;
}
