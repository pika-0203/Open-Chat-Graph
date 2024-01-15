<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface LogRepositoryInterface
{
    public function logAddOpenChat(int $open_chat_id, string $ip, string $ua): void;

    public function logAddOpenChatDuplicationError(int $open_chat_id, string $url, string $ip, string $ua): void;

    public function logAddOpenChatError(string $ip, string $ua, string $message): void;

    public function logUpdateOpenChatError(int $open_chat_id, string $message): void;

    public function logOpenChatImageStoreError(string $imgIdentifier, string $message): void;

    public function getNumAddOpenChatPerMinute(string $ip): int;

    public function getRecentLog(): string;
}
