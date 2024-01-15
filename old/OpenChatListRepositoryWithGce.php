<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;
use App\Models\GCE\DBGce;

//final class OpenChatListRepositoryWithGce extends OpenChatListRepository implements OpenChatListRepositoryInterface
{
    public function findByKeyword(string $keyword, int $offset, int $limit): array
    {
        [$search, $count] = $this->findIdByKeyword($keyword, $offset, $limit);
        if (!$search) {
            return ['result' => $search, 'count' => $count];
        }

        $records = $this->getRankingRecord(array_column($search, 'id'));

        $result = [];
        foreach ($search as $key => $raw) {
            foreach ($records as $key2 => $raw2) {
                if ($raw['id'] === $raw2['id']) {
                    $result[] = $search[$key] + $records[$key2];
                    break;
                }
            }
        }

        return compact('result', 'count');
    }

    private function findIdByKeyword(string $keyword, int $offset, int $limit): array
    {
        $query = fn ($where) =>
        "(
            SELECT
                id,
                CASE 
                    WHEN ranking_id = 0 THEN NULL ELSE percent_increase
                END AS percent_increase,
                CASE 
                    WHEN ranking_id = 0 THEN NULL ELSE diff_member
                END AS diff_member
            FROM
                open_chat
            {$where}
            ORDER BY
                CASE
                    WHEN MATCH(name) AGAINST(:search IN BOOLEAN MODE) AND ranking_id > 0 THEN 0
                    WHEN MATCH(name) AGAINST(:search IN BOOLEAN MODE) AND ranking_id = 0 THEN 1
                    WHEN MATCH(description) AGAINST(:search IN BOOLEAN MODE) AND ranking_id > 0 THEN 2
                    ELSE 3
                END,
                CASE
                    WHEN MATCH(name) AGAINST(:search IN BOOLEAN MODE) AND ranking_id = 0 THEN -member
                    ELSE ranking_id
                END ASC,
                CASE
                    WHEN MATCH(name, description) AGAINST(:search IN BOOLEAN MODE) AND ranking_id = 0 THEN member
                    ELSE NULL
                END DESC
            LIMIT
                :offset, :limit
        )
        UNION ALL
        (
            SELECT
                count(*) AS id,
                0,
                0
            FROM
                open_chat
            {$where}
        )";

        $whereClauseQuery = "WHERE MATCH(name, description) AGAINST(:search IN BOOLEAN MODE)";

        $keyword = preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $keyword);
        $keyword = preg_replace('/\s+/u', ' ', $keyword);

        $search = DBGce::executeFulltextSearchQuery($query, $whereClauseQuery, $keyword, compact('offset', 'limit'));

        $count = array_pop($search);
        $count = $count['id'];

        return [$search, $count];
    }

    private function getRankingRecord(array $idArray): array
    {
        $statements = array_fill(0, count($idArray), "?");
        $statement = "(" . implode(',', $statements) . ")";

        $query =
            "SELECT
                id,
                name,
                img_url,
                description,
                member,
                emblem,
                category,
                is_alive
            FROM
                open_chat
            WHERE
                id IN {$statement}";

        $stmt = DB::prepare($query);
        $stmt->execute($idArray);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
