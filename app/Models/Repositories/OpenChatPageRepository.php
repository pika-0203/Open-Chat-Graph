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
                oc.id,
                oc.name,
                oc.url,
                oc.img_url,
                oc.description,
                oc.member,
                oc.api_created_at,
                oc.emblem,
                oc.category,
                oc.emid,
                UNIX_TIMESTAMP(oc.created_at) AS created_at,
                UNIX_TIMESTAMP(oc.updated_at) AS updated_at,
                is_alive
            FROM
                open_chat AS oc
            WHERE
                oc.id = :id";

        return DB::fetch($query, ['id' => $id]);
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
