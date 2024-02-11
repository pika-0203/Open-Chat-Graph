<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Services\OpenChat\Dto\OpenChatRepositoryDto;

interface OpenChatDataForUpdaterWithCacheRepositoryInterface
{
    public static function clearCache(): void;

    public static function cacheOpenChatData(bool $excludeData = false): void;

    /**
     * @return OpenChatRepositoryDto|false
     */
    public function getOpenChatDataById(int $id): OpenChatRepositoryDto|false;

    /**
     * @return OpenChatRepositoryDto|false
     */
    public function getOpenChatDataByEmid(string $emid): OpenChatRepositoryDto|false;

    public function getOpenChatIdByEmid(string $emid): int|false;
}
