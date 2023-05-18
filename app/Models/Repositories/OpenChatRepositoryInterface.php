<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface OpenChatRepositoryInterface
{
    /**
     * @return array|false
     * `['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'is_alive' => int, 'diff_member' => int|null, percent_increase => float|null]`
     */
    public function getOpenChatById(int $id): array|false;

    public function findDuplicateOpenChat(string $name, string $description, string $img_url): int|false;

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
