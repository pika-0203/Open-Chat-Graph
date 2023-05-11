<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class UpdateOpenChatRepository implements UpdateOpenChatRepositoryInterface
{
    public function updateOpenChat(
        int $id,
        ?bool $is_alive = null,
        ?string $name = null,
        ?string $img_url = null,
        ?string $description = null,
        ?int $member = null,
    ): void {
        $params = compact('name', 'img_url', 'description', 'member', 'is_alive');

        $columnsToUpdate = array_filter($params, fn ($param) => $param !== null, ARRAY_FILTER_USE_BOTH);
        $setStatement = '';
        if (!empty($columnsToUpdate)) {
            $setStatement = implode(
                ', ',
                array_map(fn ($column) => "{$column} = :{$column}", array_keys($columnsToUpdate))
            );
            $setStatement .= ', ';
        }

        $columnsToUpdate += ['id' => $id];

        $query = "UPDATE open_chat SET {$setStatement} updated_at = NOW() WHERE id = :id";

        if (DB::execute($query, $columnsToUpdate)->rowCount() === 0) {
            throw new \RuntimeException('レコードを更新出来ませんでした。');
        }
    }

    public function getOpenChatIdByPeriod(int $before_at, int $limit): array
    {
        $query =
            'SELECT
                id
            FROM
                open_chat
            WHERE
                updated_at < FROM_UNIXTIME(:before_at)
                AND is_alive = 1
            ORDER BY
                updated_at ASC
            LIMIT
                :limit';

        return DB::execute($query, compact('before_at', 'limit'))
            ->fetchAll(\PDO::FETCH_COLUMN, 0);
    }

    public function existsRecordByImgUrlExcludingId(int $open_chat_id, string $img_url): bool
    {
        $query =
            'SELECT 
                id
            FROM
                open_chat
            WHERE
                img_url = :img_url
                AND NOT id = :open_chat_id
            LIMIT
                1';

        return DB::fetch($query, compact('open_chat_id', 'img_url')) !== false;
    }

    public function deleteOpenChat(int $id): bool
    {
        $query =
            'DELETE FROM
                open_chat
            WHERE
                id = :id';

        return DB::execute($query, ['id' => $id])
            ->rowCount() > 0;
    }
}
