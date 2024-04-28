<?php

declare(strict_types=1);

namespace App\Models\CommentRepositories;

class DeleteCommentRepository implements DeleteCommentRepositoryInterface
{
    function deleteComment(int $comment_id, ?int $flag): bool
    {
        $id = compact('comment_id');

        is_null($flag) && CommentDB::execute(
            "DELETE FROM `like` WHERE comment_id = :comment_id",
            $id
        );

        return CommentDB::executeAndCheckResult(
            is_null($flag)
                ? "DELETE FROM comment WHERE comment_id = :comment_id"
                : "UPDATE comment SET flag = {$flag} WHERE comment_id = :comment_id",
            $id
        );
    }

    function deleteCommentByOcId(int $open_chat_id, int $id, ?int $flag = null): bool
    {
        $comment_id = CommentDB::fetchColumn(
            "SELECT comment_id FROM comment WHERE open_chat_id = :open_chat_id AND id = :id",
            compact('open_chat_id', 'id')
        );

        if (!$comment_id) return false;

        return $this->deleteComment($comment_id, $flag);
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
