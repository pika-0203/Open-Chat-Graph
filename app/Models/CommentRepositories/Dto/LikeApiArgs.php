<?php

namespace App\Models\CommentRepositories\Dto;

class LikeApiArgs
{
    function __construct(
        public int $comment_id,
        public string $user_id,
    ) {
    }
}
