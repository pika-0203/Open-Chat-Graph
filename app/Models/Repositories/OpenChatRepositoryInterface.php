<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Services\OpenChat\Dto\OpenChatDto;

interface OpenChatRepositoryInterface
{
    public function getOpenChatIdByUrl(string $url): int|false;

    /**
     * @return int id
     */
    public function addOpenChatFromDto(OpenChatDto $dto): int;

    /**
     * 画像のダウンロードに失敗した場合にimg_urlを"noimage"にする
     */
    public function markAsNoImage(int $id): void;

    public function markAsRegistrationByUser(int $id): void;

    public static function getInsertCount(): int;

    public static function resetInsertCount(): void;
}
