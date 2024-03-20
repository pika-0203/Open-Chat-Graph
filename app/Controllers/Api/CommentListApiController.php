<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Models\CommentRepositories\CommentListRepositoryInterface;
use App\Models\CommentRepositories\Dto\CommentListApi;
use App\Models\CommentRepositories\Dto\CommentListApiArgs;
use App\Services\Auth\AuthInterface;

class CommentListApiController
{
    function index(
        CommentListRepositoryInterface $commentListRepository,
        AuthInterface $auth,
        int $page,
        int $limit,
        int $open_chat_id
    ) {
        $args = new CommentListApiArgs(
            $page,
            $limit,
            $open_chat_id,
            $auth->loginCookieUserId()
        );

        $list = $commentListRepository->findComments($args);

        return response(array_map(fn (CommentListApi $el) => $el->getResponseArray(), $list));
    }
}
