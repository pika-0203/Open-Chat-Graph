<?php

declare(strict_types=1);

namespace App\Models\CommentRepositories;

class DeleteCommentRepository implements DeleteCommentRepositoryInterface
{
    function deleteComment(int $comment_id): void
    {
        $id = compact('comment_id');

        CommentDB::execute(
            "DELETE FROM `like` WHERE comment_id = :comment_id",
            $id
        );

        CommentDB::execute(
            "DELETE FROM comment WHERE comment_id = :comment_id",
            $id
        );
    }

    function deleteCommentsAll(int $open_chat_id): void
    {
        $id = compact('open_chat_id');

        CommentDB::execute(
            "DELETE FROM
                `like`
            WHERE
                comment_id IN (
                    SELECT
                        comment_id
                    FROM
                        comment
                    WHERE
                        open_chat_id = :open_chat_id
                )",
            $id
        );

        CommentDB::execute(
            "DELETE FROM comment WHERE open_chat_id = :open_chat_id",
            $id
        );
    }
}
