<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface UserLogoutRepositoryInterface
{
    /**
     * デバイスのトークンをDBから削除する
     */
    public function deleteDeviceToken(string $token): void;
}
