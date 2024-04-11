<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\Admin\AdminAuthService;
use App\Services\Recommend\RecommendUpdater;
use Shadow\DB;
use Shadow\Kernel\Reception;
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

    function index(Reception $r)
    {
        if ($r->has('ocTag') && $r->has('ocId')) {
            return $this->modifyTag();
        }
    }

    private function modifyTag()
    {
        $id = Reception::input('ocId');
        $tag = Reception::input('ocTag');

        if (!DB::fetchColumn('SELECT id FROM open_chat WHERE id = ' . $id))
            throw new BadRequestException("存在しないID: " . $id);

        /** @var RecommendUpdater $recommendUpdater */
        $recommendUpdater = app(RecommendUpdater::class);
        $tags = $recommendUpdater->getAllTagNames();
        if (!in_array($tag, $tags))
            throw new BadRequestException('存在しないタグ: ' . $tag);;

        DB::execute(
            "INSERT INTO modify_recommend VALUES({$id}, '{$tag}') 
                ON DUPLICATE KEY UPDATE id = {$id}, tag = '{$tag}'"
        );
        DB::execute(
            "INSERT INTO recommend VALUES({$id}, '{$tag}') 
                ON DUPLICATE KEY UPDATE id = {$id}, tag = '{$tag}'"
        );

        return redirect("oc/{$id}");
    }
}
