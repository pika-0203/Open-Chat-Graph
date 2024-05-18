<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Config\AdminConfig;
use App\Models\CommentRepositories\CommentLogRepositoryInterface;
use App\Models\CommentRepositories\CommentPostRepositoryInterface;
use App\Models\CommentRepositories\Dto\CommentPostApiArgs;
use App\Models\CommentRepositories\Enum\CommentLogType;
use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Services\Auth\AuthInterface;
use App\Services\Auth\GoogleReCaptcha;

class CommentPostApiController
{
    function index(
        CommentPostRepositoryInterface $commentPostRepository,
        CommentLogRepositoryInterface $commentLogRepository,
        OpenChatPageRepositoryInterface $openChatPageRepository,
        AuthInterface $auth,
        GoogleReCaptcha $googleReCaptcha,
        string $token,
        int $open_chat_id,
        string $name,
        string $text
    ) {
        $score = $googleReCaptcha->validate($token, 0.5);

        if ($open_chat_id && !$openChatPageRepository->isExistsOpenChat($open_chat_id)) {
            return false;
        }

        $args = new CommentPostApiArgs(
            $auth->verifyCookieUserId(),
            $open_chat_id,
            $name,
            $text
        );

        $commentId = $commentPostRepository->addComment($args);

        $commentLogRepository->addLog(
            $commentId,
            CommentLogType::AddComment,
            getIP(),
            getUA(),
            "{$score}"
        );

        purgeCacheCloudFlare(
            AdminConfig::CloudFlareZoneID,
            AdminConfig::CloudFlareApiKey,
            [url()]
        );

        return response([
            'commentId' => $commentId,
            'userId' => $args->user_id === AdminConfig::ADMIN_API_KEY ? '管理者' : base62Hash($args->user_id, 'fnv132')
        ]);
    }
}
