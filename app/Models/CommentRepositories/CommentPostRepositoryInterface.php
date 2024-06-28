<?php

namespace App\Models\CommentRepositories;

use App\Models\CommentRepositories\Dto\CommentPostApiArgs;

interface CommentPostRepositoryInterface
{
    function addComment(CommentPostApiArgs $args): int;
    function addBanRoom(int $open_chat_id): int;
    function getBanRoomWeek(int $open_chat_id): int|false;
}
