<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Models\CommentRepositories\CommentLogRepositoryInterface;
use App\Models\CommentRepositories\Dto\LikeApiArgs;
use App\Models\CommentRepositories\Enum\CommentLogType;
use App\Models\CommentRepositories\Enum\LikeBtnType;
use App\Models\CommentRepositories\LikePostRepositoryInterface;
use App\Services\Auth\AuthInterface;

class CommentLikePostApiController
{
    function __construct(
        private LikePostRepositoryInterface $likePostRepository,
        private CommentLogRepositoryInterface $commentLogRepository,
        private AuthInterface $auth,
    ) {
    }

    private function getArgs(int $comment_id): LikeApiArgs
    {
        return new LikeApiArgs(
            $comment_id,
            $this->auth->verifyCookieUserId()
        );
    }

    private function response(LikeApiArgs $args, CommentLogType $type, int $entity_id)
    {
        $this->commentLogRepository->addLog(
            $entity_id,
            $type,
            getIP(),
            getUA(),
        );

        return response($this->likePostRepository->getLikeRecord($args));
    }

    function add(int $comment_id, string $type)
    {
        $args = $this->getArgs($comment_id);

        $reslut = $this->likePostRepository->addLike($args, LikeBtnType::from($type));

        return $reslut ? $this->response($args, CommentLogType::AddLike, $reslut) : false;
    }

    function delete(int $comment_id)
    {
        $args = $this->getArgs($comment_id);

        $reslut = $this->likePostRepository->deleteLike($args);

        return $reslut ? $this->response($args, CommentLogType::DeleteLike, $args->comment_id) : false;
    }
}
