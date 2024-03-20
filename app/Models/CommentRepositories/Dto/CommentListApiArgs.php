<?php

declare(strict_types=1);

namespace App\Models\CommentRepositories\Dto;

class CommentListApiArgs
{
    function __construct(
        public int $page,
        public int $limit,
        public int $open_chat_id,
        public string $user_id,
    ) {
    }
}
