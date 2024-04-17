<?php

declare(strict_types=1);

namespace App\Services\OpenChatAdmin;

use App\Models\RecommendRepositories\RecommendRankingRepository;
use App\Services\OpenChatAdmin\Dto\AdminOpenChatDto;
use Shadow\DB;

class AdminOpenChat
{
    function __construct(
        private RecommendRankingRepository $recommendRankingRepository,
    ) {
    }

    function getDto(int $id): AdminOpenChatDto
    {
        $dto = new AdminOpenChatDto;
        $dto->id = $id;
        $dto->recommendTag = $this->recommendRankingRepository->getRecommendTag($id);
        $dto->modifyTag = DB::fetchColumn("SELECT tag FROM modify_recommend WHERE id = {$id}");;

        return $dto;
    }
}
