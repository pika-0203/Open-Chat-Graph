<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Models\CommentRepositories\DeleteCommentRepositoryInterface;
use App\Services\Admin\AdminAuthService;
use App\Services\OpenChatAdmin\AdminEndPoint;
use Shadow\Kernel\Reception;
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
        return redirect("oc/{$id}");
    }

    function deletecomment(DeleteCommentRepositoryInterface $deleteCommentRepository)
    {
        $id = Reception::input('ocId');
        $commentId = Reception::input('commentId');

        $result = $deleteCommentRepository->deleteCommentByOcId($id, $commentId);

        return $result
            ? view('admin/admin_message_page', ['title' => 'コメント削除', 'message' => '削除されたコメントはありません'])
            : redirect("oc/{$id}");
    }
}
