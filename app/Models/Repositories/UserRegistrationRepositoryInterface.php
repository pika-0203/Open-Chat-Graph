<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface UserRegistrationRepositoryInterface
{
    /**
     * OpenIDからユーザーIDを取得する
     * 
     * @return int|false user_id
     */
    public function getUserIdByOpenId(string $open_id): int|false;

    /**
     * 新規ユーザーを保存する
     * 
     * @return int user_id
     */
    public function createUser(string $open_id, string $token, int $expires): int;

    /**
     * デバイストークンを保存する
     */
    public function insertDeviceToken(int $user_id, string $token, int $expires): void;
}
