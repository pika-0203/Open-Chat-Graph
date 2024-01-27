<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatRepositoryDto;
use App\Services\OpenChat\Dto\OpenChatUpdaterDto;

class UpdateOpenChatRepository implements UpdateOpenChatRepositoryInterface
{
    public function __construct(
        private StatisticsRepositoryInterface $statisticsRepository,
        private DeleteOpenChatRepositoryInterface $DeleteOpenChatRepository
    ) {
    }

    public function getOpenChatDataById(int $id): OpenChatRepositoryDto|false
    {
        $query =
            'SELECT
                emid,
                name,
                description,
                img_url,
                member,
                api_created_at,
                category,
                emblem,
                url
            FROM
                open_chat AS oc
            WHERE
                oc.id = :id';

        $result = DB::fetch($query, ['id' => $id]);
        if (!$result) {
            return false;
        }

        return new OpenChatRepositoryDto($result);
    }

    public function updateOpenChatRecord(OpenChatUpdaterDto $dto): bool
    {
        if ($dto->delete_flag === true) {
            return $this->DeleteOpenChatRepository->deleteOpenChat($dto->open_chat_id);
        }

        $columnsToSet = [
            'updated_at' => date('Y-m-d H:i:s', $dto->updated_at),
            'emid' => $dto->emid ?? null,
            'name' => $dto->name ?? null,
            'description' => $dto->desc ?? null,
            'img_url' => $dto->profileImageObsHash ?? null,
            'member' => $dto->memberCount ?? null,
            'api_created_at' => $dto->createdAt ?? null,
            'category' => $dto->category ?? null,
            'emblem' => $dto->emblem ?? null,
            'next_update' => isset($dto->next_update) ? date('Y-m-d', $dto->next_update) : null,
        ];

        $columnsToUpdate = array_filter($columnsToSet, fn ($value) => $value !== null);
        $setStatement = implode(',', array_map(fn ($column) => "{$column} = :{$column}", array_keys($columnsToUpdate)));

        $columnsToUpdate['id'] = $dto->open_chat_id;

        $result = DB::executeAndCheckResult(
            "UPDATE 
                open_chat 
            SET 
                {$setStatement} 
            WHERE 
                id = :id",
            $columnsToUpdate
        );

        if (isset($dto->db_member)) {
            $this->statisticsRepository->insertUpdateDailyStatistics($dto->open_chat_id, ($dto->memberCount ?? $dto->db_member), $dto->updated_at);
        }

        return $result;
    }

    public function getUpdateFromApiTargetOpenChatId(?int $limit = null): array
    {
        $query =
            "SELECT
                id,
                emid AS fetcherArg
            FROM
                open_chat
            WHERE
                next_update <= :date
            ORDER BY
                updated_at ASC";

        $params = ['date' => date('Y-m-d')];

        if ($limit !== null) {
            $query .= ' LIMIT :limit';
            $params['limit'] = $limit;
        }

        return DB::fetchAll($query, $params);
    }

    public function getOpenChatIdByEmid(string $emid): array|false
    {
        $query =
            'SELECT
                id,
                next_update <= :date AS next_update
            FROM
                open_chat
            WHERE
                emid = :emid
            ORDER BY
                id ASC
            LIMIT 1';

        $date = date('Y-m-d');

        $result = DB::fetch($query, compact('emid', 'date'));
        if ($result !== false) {
            $result['next_update'] = (bool)$result['next_update'];
        }

        return $result;
    }
}
