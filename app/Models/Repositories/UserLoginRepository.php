<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class UserLoginRepository implements UserLoginRepositoryInterface
{
    public function getUserIdByToken(string $token): array|false
    {
        $query =
            'SELECT
                user_id,
                UNIX_TIMESTAMP(expires) AS expires
            FROM
                devices
            WHERE
                token = :token';

        return DB::fetch($query, ['token' => $token]);
    }

    public function updateDeviceToken(string $token, string $newToken, int $newExpires): void
    {
        $query =
            'UPDATE
                devices
            SET
                expires = FROM_UNIXTIME(:newExpires),
                token = :newToken
            WHERE
                token = :token';

        DB::execute($query, compact('token', 'newToken', 'newExpires'));
    }
}
