<?php
// TODO: ratingのカウントも取得するようクエリを修正する
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
                oc.is_alive,
                ranking.diff_member AS diff_member,
                ranking.percent_increase AS percent_increase
            FROM
                open_chat AS oc
                LEFT JOIN statistics_ranking AS ranking ON ranking.open_chat_id = oc.id
            WHERE
                oc.id = :id';

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
                url = :url';

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
