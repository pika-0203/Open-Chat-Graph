<?php

namespace App\Models\CommentRepositories;

use App\Models\CommentRepositories\Dto\LikeApiArgs;
use App\Models\CommentRepositories\Dto\LikeBtnApi;
use App\Models\CommentRepositories\Enum\LikeBtnType;

interface LikePostRepositoryInterface
{
    function addLike(LikeApiArgs $args, LikeBtnType $type): int;
    function deleteLike(LikeApiArgs $args): bool;
    function getLikeRecord(LikeApiArgs $args): LikeBtnApi;
    function getLikeAll(): array;
}
