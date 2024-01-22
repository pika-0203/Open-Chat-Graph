<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Config\AppConfig;
use Shadow\DB;
use App\Services\OpenChat\Dto\OpenChatDto;

class DuplicateOpenChatRepository
{
    public function getDuplicateOpenChatInfo(): array
    {
        $defaultImgs = implode("', '", AppConfig::DEFAULT_OPENCHAT_IMG_URL);

        $query =
            "SELECT
                GROUP_CONCAT(id) as id,
                img_url
            FROM
                open_chat
            WHERE
                img_url NOT IN (
                    '{$defaultImgs}',
                    'noimage'
                )
            GROUP BY
                img_url
            HAVING
                COUNT(*) > 1";

        $result = DB::fetchAll($query);

        foreach ($result as &$oc) {
            $oc['id'] = array_map(fn ($id) => (int)$id, explode(',', $oc['id']));
        }

        return $result;
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
}
