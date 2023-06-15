<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class LogRepository implements LogRepositoryInterface
{
    public function logAddOpenChat(int $open_chat_id, string $ip, string $ua): void
    {
        $this->insertLog('AddOpenChat', $ip, $ua, (string)$open_chat_id);
    }

    public function logUpdateOpenChatError(int $open_chat_id, string $ip, string $ua, string $message): void
    {
        $this->insertLog('UpdateOpenChat', $ip, $ua, "id: {$open_chat_id} error: {$message}");
    }

    public function logAddOpenChatDuplicationError(int $open_chat_id, string $url, string $ip, string $ua): void
    {
        $this->insertLog('AddOpenChatDuplicationError', $ip, $ua, "id: {$open_chat_id} url: {$url}");
    }

    public function logAddOpenChatError(string $ip, string $ua, string $message): void
    {
        $this->insertLog('AddOpenChatError', $ip, $ua, $message);
    }

    private function insertLog(string $type, string $ip, string $ua, string $message = ''): void
    {
        $query =
            'INSERT INTO
                user_log (type, message, ip, ua)
            VALUES
                (:type, :message, :ip, :ua)';

        DB::execute($query, compact('type', 'ip', 'ua', 'message'));
    }
}
