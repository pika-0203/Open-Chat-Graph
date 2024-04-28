<?php

namespace App\Models\CommentRepositories;

interface DeleteCommentRepositoryInterface
{
    function deleteComment(int $comment_id, bool $delete): bool;
    function deleteCommentByOcId(int $open_chat_id, int $id, bool $delete = false): bool;
    function deleteCommentsAll(int $open_chat_id): void;
}
