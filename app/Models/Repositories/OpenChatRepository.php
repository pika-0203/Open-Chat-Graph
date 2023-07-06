<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class OpenChatRepository implements OpenChatRepositoryInterface
{
    public function getOpenChatById(int $id): array|false
    {
        $query =
            'SELECT
                oc.id,
                oc.name,
                oc.url,
                oc.img_url,
                oc.description,
                oc.member,
                UNIX_TIMESTAMP(oc.created_at) AS created_at,
                UNIX_TIMESTAMP(oc.updated_at) AS updated_at,
                oc.is_alive,
                ranking.diff_member AS diff_member,
                ranking.percent_increase AS percent_increase,
                ranking2.diff_member AS diff_member2,
                ranking2.percent_increase AS percent_increase2
            FROM
                open_chat AS oc
                LEFT JOIN statistics_ranking AS ranking ON ranking.open_chat_id = oc.id
                LEFT JOIN statistics_ranking2 AS ranking2 ON ranking2.open_chat_id = oc.id
            WHERE
                oc.id = :id
                AND is_alive = 1';

        return DB::fetch($query, ['id' => $id]);
    }

    public function findDuplicateOpenChat(string $name, string $description, string $img_url): int|false
    {
        $query =
            'SELECT
                id
            FROM
                open_chat
            WHERE
                name = :name
                AND description = :description
                AND img_url = :img_url
                AND is_alive = 1
            LIMIT 1';

        return DB::execute($query, compact('name', 'description', 'img_url'))->fetchColumn();
    }

    public function getOpenChatIdByUrl(string $url): int|false
    {
        $query =
            'SELECT
                id
            FROM
                open_chat
            WHERE
                url = :url
                AND is_alive = 1';

        return DB::execute($query, ['url' => $url])->fetchColumn();
    }

    public function addOpenChat(
        string $name,
        string $url,
        string $img_url,
        string $description,
        int $member,
    ): int {
        $addOpenChat =
            'INSERT INTO
                open_chat (name, url, img_url, description, member)
            VALUES
                (:name, :url, :img_url, :description, :member)';

        return DB::executeAndGetLastInsertId($addOpenChat, compact('name', 'url', 'img_url', 'description', 'member'));
    }
}
