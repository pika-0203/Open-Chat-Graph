<?php

namespace App\Models\CommentRepositories;

interface DeleteCommentRepositoryInterface
{
    function deleteComment(int $comment_id): void;
    function deleteCommentsAll(int $open_chat_id): void;
}
