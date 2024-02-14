<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Services\OpenChat\Dto\OpenChatDto;

interface OpenChatRepositoryInterface
{
    /**
     * @return int id
     */
    public function addOpenChatFromDto(OpenChatDto $dto): int|false;

    public static function getInsertCount(): int;

    public static function resetInsertCount(): void;

    /**
     * @return array{ id: int, emid: string }[]
     */
    public function getOpenChatIdEmidArrayAll(): array;

    /**
     * @return int[] open_chat_id
     */
    public function getOpenChatIdAll(): array;

    /**
     * @param string $date Y-m-d
     * @return int[]
     */
    public function getOpenChatIdAllByCreatedAtDate(string $date): array;
}
