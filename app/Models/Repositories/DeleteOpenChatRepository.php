<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;
use App\Models\Repositories\RankingPosition\RankingPositionRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;

class DeleteOpenChatRepository implements DeleteOpenChatRepositoryInterface
{
    public function __construct(
        private StatisticsRepositoryInterface $statisticsRepository,
        private RankingPositionRepositoryInterface $rankingPositionRepository
    ) {
    }

    private static array $deleteOpenChat = [];

    public static function getDeletedOpenChat(): array
    {
        return self::$deleteOpenChat;
    }

    public function deleteOpenChat(int $open_chat_id): bool
    {
        self::$deleteOpenChat[] = $open_chat_id;

        $result = DB::executeAndCheckResult(
            "DELETE FROM
                     open_chat
                WHERE
                     id = :open_chat_id",
            compact('open_chat_id')
        );

        $this->statisticsRepository->daleteDailyStatistics($open_chat_id);
        $this->rankingPositionRepository->daleteDailyPosition($open_chat_id);

        return $result;
    }

    public function deleteDuplicatedOpenChat(int $duplicated_id, int $open_chat_id): void
    {
        $getEmid = fn ($id) => DB::fetchColumn(
            'SELECT
                emid
            FROM
                open_chat
            WHERE
                id = :id',
            compact('id')
        );

        if (!$getEmid($open_chat_id)) {
            $emid = $getEmid($duplicated_id);
            $emid && DB::execute(
                'UPDATE
                    open_chat
                SET
                    emid = :emid
                WHERE
                    id = :open_chat_id',
                compact('open_chat_id', 'emid')
            );
        }

        $this->statisticsRepository->mergeDuplicateOpenChatStatistics($duplicated_id, $open_chat_id);
        $this->rankingPositionRepository->mergeDuplicateDailyPosition($duplicated_id, $open_chat_id);
        $this->deleteOpenChat($duplicated_id);

        DB::execute(
            'UPDATE
                open_chat
            SET
                is_alive = 1
            WHERE
                id = :open_chat_id',
            compact('open_chat_id')
        );

        DB::execute(
            'INSERT INTO
                open_chat_merged (duplicated_id, open_chat_id)
            VALUES
                (:duplicated_id, :open_chat_id)',
            compact('duplicated_id', 'open_chat_id')
        );
    }
}
