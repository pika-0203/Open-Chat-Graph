<?php

declare(strict_types=1);

namespace App\Models\CommentRepositories;

use App\Models\CommentRepositories\Dto\CommentListApiArgs;
use App\Models\CommentRepositories\Dto\CommentListApi;

class CommentListRepository implements CommentListRepositoryInterface
{
    function findComments(CommentListApiArgs $args): array
    {
        $query =
            "SELECT
                c.id,
                c.comment_id AS commentId,
                c.name,
                c.text,
                c.time,
                c.user_id AS userId,
                IFNULL(l.empathy, 0) AS empathyCount,
                IFNULL(l.insights, 0) AS insightsCount,
                IFNULL(l.negative, 0) AS negativeCount,
                IFNULL(l.voted, '') AS voted
            FROM
                comment AS c
                LEFT JOIN (
                    SELECT
                        comment_id,
                        COUNT(
                            CASE
                                WHEN type = 'empathy' THEN 1
                            END
                        ) AS empathy,
                        COUNT(
                            CASE
                                WHEN type = 'insights' THEN 1
                            END
                        ) AS insights,
                        COUNT(
                            CASE
                                WHEN type = 'negative' THEN 1
                            END
                        ) AS negative,
                        GROUP_CONCAT(
                            CASE
                                WHEN user_id = :user_id THEN type
                                ELSE NULL
                            END
                        ) AS voted
                    FROM
                        `like`
                    GROUP BY
                        comment_id
                ) AS l ON l.comment_id = c.comment_id
            WHERE
                c.open_chat_id = :open_chat_id
            ORDER BY
                c.comment_id DESC
            LIMIT
                :offset, :limit";

        return CommentDB::fetchAll($query, [
            'user_id' => $args->user_id,
            'open_chat_id' => $args->open_chat_id,
            'offset' => $args->page * $args->limit,
            'limit' => $args->limit,
        ], [\PDO::FETCH_CLASS, CommentListApi::class]);
    }

    function findCommentById(int $comment_id): array
    {
        $query =
            "SELECT
                comment_id,
                open_chat_id,
                id,
                name,
                text,
                time
            FROM
                comment
            WHERE
                comment_id = :comment_id";

        return CommentDB::fetch($query, compact('comment_id'));
    }
}
