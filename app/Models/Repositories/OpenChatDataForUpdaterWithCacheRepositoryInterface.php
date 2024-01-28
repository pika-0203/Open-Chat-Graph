<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Services\OpenChat\Dto\OpenChatRepositoryDto;

interface OpenChatDataForUpdaterWithCacheRepositoryInterface
{
    public static function clearCache(): void;

    public static function addOpenChatIdByEmidCache(int $id, string $emid): void;

    /**
     * @return array|false `['id' => int, next_update => 0 or 1, img_url => string]`
     */
    public function getOpenChatIdByEmid(string $emid): array|false;

    /**
     * @return OpenChatRepositoryDto|false
     */
    public function getOpenChatDataById(int $id): OpenChatRepositoryDto|false;

    /**
     * 過去一週間でメンバー数に変化があったかを調べる
     */
    public function getMemberChangeWithinLastWeek(int $open_chat_id): bool;
}