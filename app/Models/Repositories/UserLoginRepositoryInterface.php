<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface UserLoginRepositoryInterface
{
    /**
     * ユーザー情報を取得する
     * 
     * @return array `['user_id' => int, 'expires' => int]`
     */
    public function getUserIdByToken(string $token): array|false;

    /**
     * デバイスのトークンを更新する
     * 
     * @return array `['token' => string, 'expires' => int]`
     */
    public function updateDeviceToken(string $token, string $newToken, int $newExpires): void;
}
