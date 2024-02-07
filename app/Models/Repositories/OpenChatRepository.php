<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;

class OpenChatRepository implements OpenChatRepositoryInterface
{
    private static int $insertCount = 0;

    public function __construct(
        private StatisticsRepositoryInterface $statisticsRepository
    ) {
    }

    public static function getInsertCount(): int
    {
        return static::$insertCount;
    }

    public static function resetInsertCount(): void
    {
        static::$insertCount = 0;
    }

    public function addOpenChatFromDto(OpenChatDto $dto): int|false
    {
        $dto->registered_open_chat_id = DB::executeAndGetLastInsertId(
            "INSERT IGNORE INTO
                open_chat (
                    name,
                    img_url,
                    description,
                    member,
                    created_at,
                    updated_at,
                    next_update,
                    emid,
                    category,
                    api_created_at,
                    emblem
                )
            VALUES
                (
                    :name,
                    :img_url,
                    :description,
                    :member,
                    FROM_UNIXTIME(:created_at),
                    FROM_UNIXTIME(:created_at),
                    :next_update,
                    :emid,
                    :category,
                    :api_created_at,
                    :emblem
                )",
            [
                'name' => $dto->name,
                'description' => $dto->desc,
                'img_url' => $dto->profileImageObsHash,
                'member' => $dto->memberCount,
                'emid' => $dto->emid,
                'created_at' => $dto->registered_created_at,
                'next_update' => $dto->getNextUpdate(),
                'api_created_at' => $dto->createdAt,
                'category' => $dto->category,
                'emblem' => $dto->emblem,
            ]
        );

        if(!$dto->registered_open_chat_id) {
            return false;
        }

        $this->statisticsRepository->addNewOpenChatStatisticsFromDto($dto);

        static::$insertCount++;
        return $dto->registered_open_chat_id;
    }

    public function getOpenChatIdEmidArrayAll(): array
    {
        return DB::fetchAll("SELECT id, emid FROM open_chat");
    }
}
