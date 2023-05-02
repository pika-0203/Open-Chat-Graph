<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class HandleNameRepository implements HandleNameRepositoryInterface
{
    public function addHandleName(int $user_id, int $open_chat_id, string $name, string $img): int
    {
        $query =
            'INSERT INTO
                handle_names (user_id, open_chat_id, name, img)
            SELECT
                :user_id,
                :open_chat_id,
                :name,
                :img
            WHERE
                EXISTS (
                    SELECT
                        1
                    FROM
                        open_chat
                    WHERE
                        open_chat_id = :open_chat_id
                )
                AND NOT EXISTS (
                    SELECT
                        1
                    FROM
                        handle_names
                    WHERE
                        user_id = :user_id
                        AND open_chat_id = :open_chat_id
                )';

        return DB::executeAndGetLastInsertId($query, compact('user_id', 'open_chat_id', 'name', 'img'));
    }

    public function getHandleName(int $user_id, int $open_chat_id): array|false
    {
        $query =
            'SELECT
                id,
                name,
                img
            FROM
                handle_names
            WHERE
                user_id = :user_id
                AND open_chat_id = :open_chat_id';

        return DB::fetch($query, compact('user_id', 'open_chat_id'));
    }
}
