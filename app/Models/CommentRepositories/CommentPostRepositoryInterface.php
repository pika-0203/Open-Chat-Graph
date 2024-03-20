<?php

namespace App\Models\CommentRepositories;

use App\Models\CommentRepositories\Dto\CommentPostApiArgs;

interface CommentPostRepositoryInterface
{
    function addComment(CommentPostApiArgs $args): int;
}
