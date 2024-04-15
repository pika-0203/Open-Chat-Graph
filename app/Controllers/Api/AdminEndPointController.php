<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Models\CommentRepositories\DeleteCommentRepositoryInterface;
use App\Services\Admin\AdminAuthService;
use App\Services\Recommend\RecommendUpdater;
use Shadow\DB;
use Shadow\Kernel\Reception;
use Shadow\Kernel\Validator;
use Shared\Exceptions\BadRequestException;
use Shared\Exceptions\NotFoundException;

class AdminEndPointController
{
    function __construct(AdminAuthService $adminAuthService)
    {
        if (!$adminAuthService->auth()) {
            throw new NotFoundException;
        }
    }

    function modifyTag(string $id, string $tag)
    {
        if (!DB::fetchColumn('SELECT id FROM open_chat WHERE id = ' . $id))
            throw new BadRequestException("存在しないID: " . $id);

        if ($tag) {
            /** @var RecommendUpdater $recommendUpdater */
            $recommendUpdater = app(RecommendUpdater::class);
            $tags = $recommendUpdater->getAllTagNames();
            if (!in_array($tag, $tags)) throw new BadRequestException('存在しないタグ: ' . $tag);;

            DB::execute(
                "INSERT INTO modify_recommend (id, tag) VALUES({$id}, '{$tag}') 
                    ON DUPLICATE KEY UPDATE id = {$id}, tag = '{$tag}'"
            );
            DB::execute(
                "INSERT INTO recommend VALUES({$id}, '{$tag}') 
                    ON DUPLICATE KEY UPDATE id = {$id}, tag = '{$tag}'"
            );
        } else {
            DB::execute(
                "INSERT INTO modify_recommend (id, tag) VALUES({$id}, '') 
                    ON DUPLICATE KEY UPDATE id = {$id}, tag = ''"
            );
            DB::execute(
                "DELETE FROM recommend WHERE id = {$id}"
            );
        }


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
