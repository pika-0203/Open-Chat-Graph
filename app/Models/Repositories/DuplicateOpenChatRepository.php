<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class DuplicateOpenChatRepository implements DuplicateOpenChatRepositoryInterface
{
    public function getDuplicateOpenChatInfo(): array
    {
        $query =
            "SELECT
                GROUP_CONCAT(id) as id
            FROM
                open_chat
            GROUP BY
                emid
            HAVING
                COUNT(*) > 1";

        $result = DB::fetchAll($query);

        $idArray = [];
        foreach ($result as $oc) {
            $idArray[] = array_map(fn ($id) => (int)$id, explode(',', $oc['id']));
        }

        return $idArray;
    }
}
