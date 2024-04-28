<?php

namespace App\Models\CommentRepositories;

interface DeleteCommentRepositoryInterface
{
    function deleteComment(int $comment_id, ?int $flag): bool;
    function deleteCommentByOcId(int $open_chat_id, int $id, ?int $flag = null): bool;
    function deleteCommentsAll(int $open_chat_id): void;
}
