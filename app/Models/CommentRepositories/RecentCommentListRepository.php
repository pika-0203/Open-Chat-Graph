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
                    ) AS rn,
                    text
                FROM
                    comment
                WHERE
                    open_chat_id != 0
            )
            SELECT
                open_chat_id,
                time,
                text
            FROM
                RankedComments
            WHERE
                rn = 1
            ORDER BY
                time DESC
            LIMIT
                :offset, :limit;";

        $comments = CommentDB::fetchAll($query, compact('offset', 'limit'));

        $ids = array_column($comments, 'open_chat_id');
        $ids = implode(',', $ids);

        $ocQuery =
            "SELECT
                oc.id,
                oc.name,
                oc.img_url,
                --oc.category,
                oc.emblem
            FROM
                open_chat AS oc
            WHERE
                id IN ({$ids})
            ORDER BY FIELD(id, {$ids})";


        $oc = DB::fetchAll($ocQuery);

        $result = [];
        foreach ($oc as $i => $el) {
            $result[] = $el + ['time' => $comments[$i]['time'],'description' => $comments[$i]['text']];
        }

        return $result;
    }
}
