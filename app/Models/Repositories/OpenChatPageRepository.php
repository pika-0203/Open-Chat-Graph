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
                oc.local_img_url AS img_url,
                oc.img_url AS api_img_url,
                oc.description,
                oc.member,
                oc.api_created_at,
                oc.emblem,
                oc.category,
                oc.emid,
                oc.updated_at,
                oc.url,
                oc.created_at,
                oc.updated_at
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
