<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Services\OpenChat\Dto\OpenChatRepositoryDto;

interface OpenChatDataForUpdaterWithCacheRepositoryInterface
{
    public static function clearCache(): void;

    public static function addOpenChatIdByEmidCache(int $id, string $emid, string $img_url): void;

    /**
     * @return false|array{ id: int, next_update: int, img_url: string } next_update:  0 | 1
     */
    public function getOpenChatIdByEmid(string $emid): false|array;

    /**
     * @return OpenChatRepositoryDto|false
     */
    public function getOpenChatDataById(int $id): OpenChatRepositoryDto|false;

    /**
     * 過去一週間でメンバー数に変化があったかを調べる
     */
    public function getMemberChangeWithinLastWeek(int $open_chat_id): bool;
}
