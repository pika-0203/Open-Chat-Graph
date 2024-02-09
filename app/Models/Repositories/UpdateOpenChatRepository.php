<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;
use App\Services\OpenChat\Dto\OpenChatRepositoryDto;
use App\Services\OpenChat\Dto\OpenChatUpdaterDto;

class UpdateOpenChatRepository implements UpdateOpenChatRepositoryInterface
{
    public function __construct(
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

        return new OpenChatRepositoryDto($id, $result);
    }

    public function updateOpenChatRecord(OpenChatUpdaterDto $dto): void
    {
        if ($dto->delete_flag === true) {
            $this->DeleteOpenChatRepository->deleteOpenChat($dto->open_chat_id);
            return;
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
        ];

        $columnsToUpdate = array_filter($columnsToSet, fn ($value) => $value !== null);
        $setStatement = implode(',', array_map(fn ($column) => "{$column} = :{$column}", array_keys($columnsToUpdate)));

        $columnsToUpdate['id'] = $dto->open_chat_id;

        DB::execute(
            "UPDATE 
                open_chat 
            SET 
                {$setStatement} 
            WHERE 
                id = :id",
            $columnsToUpdate
        );
    }

    public function getOpenChatIdByEmid(string $emid): int|false
    {
        $query =
            'SELECT
                id
            FROM
                open_chat
            WHERE
                emid = :emid';

        return DB::fetchColumn($query, compact('emid'));
    }

    public function getOpenChatIdAll(): array
    {
        $query =
            'SELECT
                id
            FROM
                open_chat';

        return DB::fetchAll($query, null, [\PDO::FETCH_COLUMN, 0]);
    }

    public function updateMemberColumn(array $oc): void
    {
        $member = $oc['member'];
        $id = $oc['open_chat_id'];
        DB::$pdo->exec("UPDATE open_chat SET member = {$member} WHERE id = {$id}");
    }
}
