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
     * 過去一週間でメンバー数に変化があったかを調べる
     */
    public function getMemberChangeWithinLastWeek(int $open_chat_id): bool;

    /**
     * アーカイブにコピーする
     */
    public function copyToOpenChatArchive(ArchiveFlagsDto $archiveFlagsDto): bool;

    public function deleteDuplicateOpenChat(int $duplicated_id, int $open_chat_id): void;

    /**
     * @return array `[['id' => array, 'img_url' => string]]`
     */
    public function getDuplicateOpenChatInfo(): array;

    /**
     * @return array|false `['id' => int, next_update => bool]`
     */
    public function getOpenChatIdByEmid(string $emid): array|false;

    public function getOpenChatUrlById(int $open_chat_id): string|false;
}
