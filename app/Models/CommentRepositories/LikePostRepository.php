<?php

declare(strict_types=1);

namespace App\Models\CommentRepositories;

use App\Models\CommentRepositories\Dto\LikeApiArgs;
use App\Models\CommentRepositories\Dto\LikeBtnApi;
use App\Models\CommentRepositories\Enum\LikeBtnType;

class LikePostRepository implements LikePostRepositoryInterface
{
    function addLike(LikeApiArgs $args, LikeBtnType $type): bool
    {
        $query =
            "INSERT INTO
                `like` (comment_id, user_id, type)
            SELECT
                comment_id,
                :user_id,
                :type
            FROM
                comment
            WHERE
                comment_id = :comment_id
                AND NOT EXISTS (
                    SELECT
                        *
                    FROM
                        `like`
                    WHERE
                        user_id = :user_id
                        AND comment_id = :comment_id
                )";

        return CommentDB::executeAndCheckResult($query, [
            'comment_id' => $args->comment_id,
            'user_id' => $args->user_id,
            'type' => $type->value
        ]);
    }

    function deleteLike(LikeApiArgs $args): bool
    {
        $query =
            "DELETE FROM
                `like`
            WHERE
                comment_id = :comment_id
                AND user_id = :user_id";

        return CommentDB::executeAndCheckResult($query, [
            'comment_id' => $args->comment_id,
            'user_id' => $args->user_id,
        ]);
    }

    function getLikeRecord(LikeApiArgs $args): LikeBtnApi
    {
        $query =
            "SELECT
                COUNT(
                    CASE
                        WHEN type = 'empathy' THEN 1
                    END
                ) AS empathyCount,
                COUNT(
                    CASE
                        WHEN type = 'insights' THEN 1
                    END
                ) AS insightsCount,
                COUNT(
                    CASE
                        WHEN type = 'negative' THEN 1
                    END
                ) AS negativeCount,
                IFNULL(
                    GROUP_CONCAT(
                        CASE
                            WHEN user_id = :user_id THEN type
                            ELSE NULL
                        END
                    ),
                    ''
                ) AS voted
            FROM
                `like`
            WHERE
                comment_id = :comment_id
            GROUP BY
                comment_id";

        $result = CommentDB::fetch($query, [
            'user_id' => $args->user_id,
            'comment_id' => $args->comment_id,
        ], [\PDO::FETCH_CLASS, LikeBtnApi::class]);

        return $result ? $result : new LikeBtnApi;
    }
}
