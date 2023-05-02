<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface LogRepositoryInterface
{
    public function logLoginSuccess(int $user_id, string $ip, string $ua): void;

    public function logLogout(int $user_id, string $ip, string $ua): void;

    public function logLoginWithOpenIdSuccess(int $user_id, string $ip, string $ua): void;

    public function logCreateAccountWithOpenId(int $user_id, string $ip, string $ua): void;

    public function logLoginError(int $user_id, string $ip, string $ua, string $message): void;

    public function logAddOpenChat(int $user_id, int $open_chat_id, string $ip, string $ua): void;

    public function logAddOpenChatDuplicationError(int $user_id, int $open_chat_id, string $url, string $ip, string $ua): void;

    public function logAddOpenChatError(int $user_id, string $ip, string $ua, string $message): void;

    public function logUpdateOpenChatError(int $user_id, int $open_chat_id, string $ip, string $ua, string $message): void;

    public function logAddReview(int $user_id, int $open_chat_id, string $ip, string $ua): void;

    public function logAddReviewError(int $user_id, string $ip, string $ua, string $message): void;
}
