<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class LogRepository implements LogRepositoryInterface
{
    public function logLoginSuccess(int $user_id, string $ip, string $ua): void
    {
        $this->insertLog('LoginSuccess', $user_id, $ip, $ua);
    }

    public function logLogout(int $user_id, string $ip, string $ua): void
    {
        $this->insertLog('Logout', $user_id, $ip, $ua);
    }

    public function logLoginWithOpenIdSuccess(int $user_id, string $ip, string $ua): void
    {
        $this->insertLog('LoginWithOpenIdSuccess', $user_id, $ip, $ua);
    }

    public function logCreateAccountWithOpenId(int $user_id, string $ip, string $ua): void
    {
        $this->insertLog('CreateAccountWithOpenId', $user_id, $ip, $ua);
    }

    public function logLoginError(int $user_id, string $ip, string $ua, string $message): void
    {
        $this->insertLog('LoginError', $user_id, $ip, $ua, $message);
    }

    public function logAddOpenChat(int $user_id, int $open_chat_id, string $ip, string $ua): void
    {
        $this->insertLog('AddOpenChat', $user_id, $ip, $ua, "id: {$open_chat_id}");
    }

    public function logUpdateOpenChatError(int $user_id, int $open_chat_id, string $ip, string $ua, string $message): void
    {
        $this->insertLog('UpdateOpenChat', $user_id, $ip, $ua, "id: {$open_chat_id} error: {$message}");
    }

    public function logAddOpenChatDuplicationError(int $user_id, int $open_chat_id, string $url, string $ip, string $ua): void
    {
        $this->insertLog('AddOpenChatDuplicationError', $user_id, $ip, $ua, "id: {$open_chat_id} url: {$url}");
    }

    public function logAddOpenChatError(int $user_id, string $ip, string $ua, string $message): void
    {
        $this->insertLog('AddOpenChatError', $user_id, $ip, $ua, $message);
    }

    public function logAddReview(int $user_id, int $open_chat_id, string $ip, string $ua): void
    {
        $this->insertLog('AddReview', $user_id, $ip, $ua, "id: {$open_chat_id}");
    }

    public function logAddReviewError(int $user_id, string $ip, string $ua, string $message): void
    {
        $this->insertLog('AddReviewError', $user_id, $ip, $ua, $message);
    }

    private function insertLog(string $type, int $user_id, string $ip, string $ua, string $message = ''): void
    {
        $query =
            'INSERT INTO
                user_log (type, message, user_id, ip, ua)
            VALUES
                (:type, :message, :user_id, :ip, :ua)';

        DB::execute($query, compact('type', 'user_id', 'ip', 'ua', 'message'));
    }
}
