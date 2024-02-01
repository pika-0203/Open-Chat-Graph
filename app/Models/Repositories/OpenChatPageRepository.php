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
                UNIX_TIMESTAMP(oc.updated_at) AS updated_at,
                r_day.diff_member AS diff_member,
                r_day.percent_increase AS percent_increase,
                r_week.diff_member AS diff_member2,
                r_week.percent_increase AS percent_increase2
            FROM
                open_chat AS oc
                LEFT JOIN statistics_ranking_day AS r_day ON oc.id = r_day.open_chat_id
                LEFT JOIN statistics_ranking_week AS r_week ON oc.id = r_week.open_chat_id
            WHERE
                oc.id = :id";

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
}
