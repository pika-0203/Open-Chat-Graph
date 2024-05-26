<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Config\AdminConfig;
use App\Models\CommentRepositories\DeleteCommentRepositoryInterface;
use App\Services\Admin\AdminAuthService;
use App\Services\OpenChatAdmin\AdminEndPoint;
use Shared\Exceptions\NotFoundException;

class AdminEndPointController
{
    function __construct(AdminAuthService $adminAuthService)
    {
        if (!$adminAuthService->auth()) {
            throw new NotFoundException;
        }
    }

    function index(string $type, string $id, AdminEndPoint $adminEndPoint)
    {
        $adminEndPoint->{$type}($id);

        purgeCacheCloudFlare(
            AdminConfig::CloudFlareZoneID,
            AdminConfig::CloudFlareApiKey,
            [
                url("oc/{$id}"),
                url("oc/{$id}?limit=hour"),
                url("oc/{$id}?limit=month"),
                url("oc/{$id}?limit=all"),
            ]
        );

        return redirect("oc/{$id}");
    }

    function deletecomment(int $commentId, int $id, int $flag, DeleteCommentRepositoryInterface $deleteCommentRepository)
    {
        $result = $deleteCommentRepository->deleteCommentByOcId($id, $commentId, $flag !== 3 ? $flag : null);
        if (!$result) {
            return view('admin/admin_message_page', ['title' => 'コメント削除', 'message' => '削除されたコメントはありません']);
        }

        if ($flag > 0) $deleteCommentRepository->deleteLikeByUserIdAndIp($id, $result['user_id'], $result['ip']);

        purgeCacheCloudFlare(
            AdminConfig::CloudFlareZoneID,
            AdminConfig::CloudFlareApiKey,
            [url()]
        );

        return redirect("oc/{$id}");
    }
}
