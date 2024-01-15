<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface OpenChatPageRepositoryInterface
{
    public function getOpenChatById(int $id): array|false;

    public function getRedirectId(int $id): int|false;

    /**
     * @return array|false
     * `['archive_id' => int, 'id' => int, 'group_id' => int,'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'created_at' => int, 'updated_at' => int, 'update_description' => int, 'update_img' => int, 'update_name' => int]`
     */
    public function getArciveById(int $open_chat_id, int $group_id): array|false;

    /**
     * @return array|false
     * `['archive_id' => int, 'id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'created_at' => int, 'updated_at' => int, 'update_description' => int, 'update_img' => int, 'update_name' => int]`
     */
    public function getNextArciveById(int $archive_id, int $open_chat_id): array|false;
}
