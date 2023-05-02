<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class UserLogoutRepository implements UserLogoutRepositoryInterface
{
    public function deleteDeviceToken(string $token): void
    {
        $query =
            'DELETE FROM
                devices
            WHERE
                token = :token';

        DB::execute($query, ['token' => $token]);
    }
}
