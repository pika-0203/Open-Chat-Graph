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

    public function updateNextUpdate(int $id, int $next_update)
    {
        $query =
            'UPDATE
                open_chat
            SET
                next_update = DATE(FROM_UNIXTIME(:next_update))
            WHERE
                id = :id';

        if (DB::execute($query, compact('id', 'next_update'))->rowCount() === 0) {
            throw new \RuntimeException('next_updateを更新出来ませんでした。');
        }
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

    public function getUpdateTargetOpenChatId(?int $limit = null): array
    {
        $query =
            'SELECT
                id
            FROM
                open_chat
            WHERE
                is_alive = 1
                AND next_update = CURDATE()
            ORDER BY
                updated_at ASC';

        if ($limit !== null) {
            $query .= ' LIMIT :limit';
            $limit = ['limit' => $limit];
        }

        return DB::execute($query, $limit ?? null)
            ->fetchAll(\PDO::FETCH_COLUMN, 0);
    }

    public function getMemberChangeWithinLastWeek(int $open_chat_id): bool
    {
        $query =
            'SELECT
                CASE
                    WHEN COUNT(DISTINCT member) > 1 THEN 1
                    WHEN MIN(`date`) > DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1
                    ELSE 0
                END AS member_change
            FROM
                statistics
            WHERE
                open_chat_id = :open_chat_id
                AND `date` BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                AND CURDATE()';

        return DB::execute($query, compact('open_chat_id'))
            ->fetchColumn() !== 0;
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
