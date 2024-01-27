<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Services\OpenChat\Dto\OpenChatRepositoryDto;
use App\Services\OpenChat\Dto\OpenChatUpdaterDto;

interface UpdateOpenChatRepositoryInterface
{
    /**
     * @return OpenChatRepositoryDto|false
     */
    public function getOpenChatDataById(int $id): OpenChatRepositoryDto|false;

    public function updateOpenChatRecord(OpenChatUpdaterDto $dto): bool;

    /**
     * @return array `['id' => int, 'fetcherArg' => emid]`
     */
    public function getUpdateFromApiTargetOpenChatId(?int $limit = null): array;

    /**
     * @return array|false `['id' => int, next_update => bool]`
     */
    public function getOpenChatIdByEmid(string $emid): array|false;
}
