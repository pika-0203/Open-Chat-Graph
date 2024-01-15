<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;
use App\Config\AppConfig;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Models\Repositories\StatisticsRepositoryInterface;

class OpenChatRepository implements OpenChatRepositoryInterface
{
    private StatisticsRepositoryInterface $statisticsRepository;
    private static int $insertCount = 0;

    public function __construct(StatisticsRepositoryInterface $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public static function getInsertCount(): int
    {
        return static::$insertCount;
    }

    public static function resetInsertCount(): void
    {
        static::$insertCount = 0;
    }

    public function findDuplicateOpenChat(OpenChatDto $dto): int|false
    {
        if (in_array($dto->profileImageObsHash, AppConfig::DEFAULT_OPENCHAT_IMG_URL, true)) {
            $params = [
                'name' => $dto->name,
                'description' => $dto->desc,
                'img_url' => $dto->profileImageObsHash,
            ];

            $query =
                'SELECT
                    id
                FROM
                    open_chat
                WHERE
                    name = BINARY :name
                    AND description = BINARY :description
                    AND img_url = :img_url
                ORDER BY
                    id ASC
                LIMIT 1';
        } else {
            $params = [
                'img_url' => $dto->profileImageObsHash,
            ];

            $query =
                'SELECT
                    id
                FROM
                    open_chat
                WHERE
                    img_url = :img_url
                ORDER BY
                    id ASC
                LIMIT 1';
        }

        return DB::execute($query, $params)->fetchColumn();
    }

    public function getOpenChatIdByUrl(string $url): int|false
    {
        $query =
            'SELECT
                id
            FROM
                open_chat
            WHERE
                url = :url
                AND is_alive = 1
            ORDER BY
                id ASC
            LIMIT 1';

        return DB::execute($query, ['url' => $url])->fetchColumn();
    }

    public function addOpenChatFromDto(OpenChatDto $dto): int
    {
        $dto->regestered_open_chat_id = DB::executeAndGetLastInsertId(
            "INSERT INTO
                open_chat (
                    name,
                    url,
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
                    :url,
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
                'created_at' => $dto->regestered_created_at,
                'next_update' => $dto->getNextUpdate(),
                'api_created_at' => $dto->createdAt,
                'category' => $dto->category,
                'emblem' => $dto->emblem,
                'url' => $dto->invitationTicket,
            ]
        );

        $this->statisticsRepository->addNewOpenChatStatisticsFromDto($dto);

        static::$insertCount++;
        return $dto->regestered_open_chat_id;
    }

    public function markAsRegistrationByUser(int $id): void
    {
        $query =
            "INSERT INTO
                user_registration_open_chat (id)
            VALUES
                (:id)";

        DB::execute($query, ['id' => $id]);
    }

    public function markAsNoImage(int $id): void
    {
        $query =
            "UPDATE
                open_chat
            SET
                img_url = 'noimage'
            WHERE
                id = :id";

        DB::execute($query, ['id' => $id]);
    }
}
