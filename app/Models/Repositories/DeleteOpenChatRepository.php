<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\CommentRepositories\DeleteCommentRepositoryInterface;
use Shadow\DB;
use App\Models\Repositories\RankingPosition\RankingPositionRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;

class DeleteOpenChatRepository implements DeleteOpenChatRepositoryInterface
{
    public function __construct(
        private StatisticsRepositoryInterface $statisticsRepository,
        private RankingPositionRepositoryInterface $rankingPositionRepository,
        private DeleteCommentRepositoryInterface $deleteCommentRepository,
    ) {
    }

    public function deleteOpenChat(int $open_chat_id): bool
    {
        $result = DB::executeAndCheckResult(
            "DELETE FROM
                     open_chat
                WHERE
                     id = :open_chat_id",
            compact('open_chat_id')
        );

        $this->statisticsRepository->daleteDailyStatistics($open_chat_id);
        $this->rankingPositionRepository->daleteDailyPosition($open_chat_id);
        
        $this->deleteCommentRepository->deleteCommentsAll($open_chat_id);

        return $result;
    }
}
