<?php

namespace App\Models\CommentRepositories;

use App\Models\CommentRepositories\Dto\CommentListApi;
use App\Models\CommentRepositories\Dto\CommentListApiArgs;

interface CommentListRepositoryInterface
{
    /** @return CommentListApi[] */
    function findComments(CommentListApiArgs $args): array;

    function findCommentById(int $comment_id): array;
}
