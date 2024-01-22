<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class OpenChatPageRepository implements OpenChatPageRepositoryInterface
{
    public function getOpenChatById(int $id): array|false
    {
        $query =
            "SELECT
                id,
                name,
                url,
                img_url,
                description,
                member,
                api_created_at,
                emblem,
                category,
                emid,
                UNIX_TIMESTAMP(created_at) AS created_at,
                UNIX_TIMESTAMP(updated_at) AS updated_at,
                is_alive
            FROM
                open_chat
            WHERE
                id = :id";

        return DB::fetch($query, ['id' => $id]);
    }

    public function getRankingPositionCategoryById(int $id): int|false
    {
        $query =
            "SELECT
                IFNULL(category, 0) AS category
            FROM
                open_chat
            WHERE
                id = :id
                AND emid IS NOT NULL";

        return DB::fetchColumn($query, ['id' => $id]);
    }

    public function getRedirectId(int $id): int|false
    {
        $query =
            "SELECT
                open_chat_id
            FROM
                open_chat_merged
            WHERE
                duplicated_id = :id";

        return DB::fetchColumn($query, ['id' => $id]);
    }

    public function getArciveById(int $open_chat_id, int $group_id): array|false
    {
        $query =
            "SELECT
                *
            FROM
                open_chat_archive
            WHERE
                group_id = :group_id
                AND id = :open_chat_id";

        return DB::fetch($query, compact('open_chat_id', 'group_id'));
    }

    public function getNextArciveById(int $archive_id, int $open_chat_id): array|false
    {
        $query =
            'SELECT
                name,
                description
            FROM
                open_chat_archive
            WHERE
                archive_id > :archive_id
                AND id = :open_chat_id
            ORDER BY
                archive_id DESC
            Limit 
                1';

        return DB::fetch($query, compact('archive_id', 'open_chat_id'));
    }
}
