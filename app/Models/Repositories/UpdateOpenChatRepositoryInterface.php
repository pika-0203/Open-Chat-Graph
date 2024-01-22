<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Services\OpenChat\Dto\OpenChatRepositoryDto;
use App\Services\OpenChat\Dto\OpenChatUpdaterDto;
use App\Services\OpenChat\Dto\ArchiveFlagsDto;

interface UpdateOpenChatRepositoryInterface
{
    /**
     * @return OpenChatRepositoryDto|false
     */
    public function getOpenChatDataById(int $id): OpenChatRepositoryDto|false;

    public function updateOpenChatRecord(OpenChatUpdaterDto $dto): bool;

    /**
     * @return array `['id' => int, 'fetcherArg' => url]`
     */
    public function getUpdateFromPageTargetOpenChatId(?int $limit = null): array;

    /**
     * @return array `['id' => int, 'fetcherArg' => emid]`
     */
    public function getUpdateFromApiTargetOpenChatId(?int $limit = null): array;

    /**
     * アーカイブにコピーする
     */
    public function copyToOpenChatArchive(ArchiveFlagsDto $archiveFlagsDto): bool;

    /**
     * @return array|false `['id' => int, next_update => bool]`
     */
    public function getOpenChatIdByEmid(string $emid): array|false;

    public function getOpenChatUrlById(int $open_chat_id): string|false;
}
