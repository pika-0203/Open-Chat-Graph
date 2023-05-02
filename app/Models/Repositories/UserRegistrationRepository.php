<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class UserRegistrationRepository implements UserRegistrationRepositoryInterface
{
    public function getUserIdByOpenId(string $open_id): int|false
    {
        $query =
            'SELECT
                user_id
            FROM
                line_open_id
            WHERE
                open_id = :open_id';

        return DB::execute($query, ['open_id' => $open_id])->fetchColumn();
    }

    public function createUser(string $open_id, string $token, int $expires): int
    {
        return (int)DB::transaction(function () use ($open_id, $token, $expires) {
            $user_id = $this->createUserId();
            $this->insertDeviceToken($user_id, $token, $expires);
            $this->insertOpenId($user_id, $open_id);
            return $user_id;
        });
    }

    public function insertDeviceToken(int $user_id, string $token, int $expires): void
    {
        $query =
            'INSERT INTO
                devices
            VALUES
                (:user_id, :token, FROM_UNIXTIME(:expires))';

        DB::execute($query, compact('user_id', 'token', 'expires'));
    }

    /**
     * usersテーブルに新規レコードを追加
     * 
     * @return int user_id
     */
    private function createUserId(): int
    {
        $query =
            'INSERT INTO
                users
            VALUES
                (null)';

        return DB::executeAndGetLastInsertId($query);
    }

    /**
     * line_open_idテーブルに新規OpenIDを保存
     */
    private function insertOpenId(int $user_id, string $open_id)
    {
        $query =
            'INSERT INTO
                line_open_id
            VALUES
                (:user_id, :open_id)';

        DB::execute($query, compact('user_id', 'open_id'));
    }
}
