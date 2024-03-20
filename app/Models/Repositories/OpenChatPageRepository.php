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
                oc.img_url,
                oc.description,
                oc.member,
                oc.api_created_at,
                oc.emblem,
                oc.category,
                oc.emid,
                UNIX_TIMESTAMP(oc.created_at) AS created_at,
                UNIX_TIMESTAMP(oc.updated_at) AS updated_at
            FROM
                open_chat AS oc
            WHERE
                oc.id = :id";

        return DB::fetch($query, ['id' => $id]);
    }

    public function isExistsOpenChat(int $id): bool
    {
        $query =
            "SELECT
                oc.id
            FROM
                open_chat AS oc
            WHERE
                oc.id = :id";

        return !!DB::fetchColumn($query, ['id' => $id]);
    }

    public function getRankingPositionCategoryById(int $id): int|false
    {
        $query =
            "SELECT
                IFNULL(category, 0) AS category
            FROM
                open_chat
            WHERE
                id = :id";

        return DB::fetchColumn($query, ['id' => $id]);
    }
}
