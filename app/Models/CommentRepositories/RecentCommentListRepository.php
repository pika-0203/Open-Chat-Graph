<?php

declare(strict_types=1);

namespace App\Models\CommentRepositories;

use App\Models\CommentRepositories\CommentDB;
use Shadow\DB;

class RecentCommentListRepository implements RecentCommentListRepositoryInterface
{
    /**
     * @return array{ id:int,name:string,img_url:string,description:string,member:int,emblem:int,category:int,time:string }[]
     */
    public function findRecentCommentOpenChat(int $offset, int $limit): array
    {
        $query =
            "WITH RankedComments AS (
                SELECT
                    open_chat_id,
                    time,
                    ROW_NUMBER() OVER (
                        PARTITION BY open_chat_id
                        ORDER BY
                            time DESC
                    ) AS rn
                FROM
                    comment
                WHERE
                    open_chat_id != 0
            )
            SELECT
                open_chat_id,
                time
            FROM
                RankedComments
            WHERE
                rn = 1
            ORDER BY
                time DESC
            LIMIT
                :offset, :limit;";

        $comments = CommentDB::fetchAll($query, compact('offset', 'limit'));

        $ocQuery =
            "SELECT
                oc.id,
                oc.name,
                oc.img_url,
                oc.description,
                oc.member,
                oc.emblem,
                oc.category
            FROM
                open_chat AS oc
            WHERE
                id = :id";

        $result = [];
        foreach ($comments as $el) {
            $oc = DB::fetch($ocQuery, ['id' => $el['open_chat_id']]);
            if (!$oc) continue;

            $result[] = $oc + ['time' => $el['time']];
        }

        return $result;
    }
}
