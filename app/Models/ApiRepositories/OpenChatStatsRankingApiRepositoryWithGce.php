<?php

declare(strict_types=1);

namespace App\Models\ApiRepositories;

use App\Models\GCE\DBGce;
use Shadow\DB;
use App\Services\Traits\TraitPaginationRecordsCalculator;

class OpenChatStatsRankingApiRepositoryWithGce
{
    use TraitPaginationRecordsCalculator;

    private function merge(array $search, array $records): array
    {
        $result = [];
        foreach ($search as $key => $raw) {
            foreach ($records as $key2 => $raw2) {
                if ($raw['id'] === $raw2['id']) {
                    $result[] = $search[$key] + $records[$key2];
                    break;
                }
            }
        }

        return $result;
    }

    function findDailyStatsRanking(OpenChatApiArgs $args): array
    {
        return array_map(
            fn ($oc) => new OpenChatListDto($oc),
            $this->getStatsRanking('statistics_ranking_day', $args)
        );
    }

    function findWeeklyStatsRanking(OpenChatApiArgs $args): array
    {
        return array_map(
            fn ($oc) => new OpenChatListDto($oc),
            $this->getStatsRanking('statistics_ranking_week', $args)
        );
    }

    private function getStatsRanking(string $tableName, OpenChatApiArgs $args): array
    {
        [$search, $count] = $this->getStatsRankingGce($tableName, $args);
        if (!$search) {
            return [];
        }

        $result = $this->merge($search, $this->getStatsRecord(array_column($search, 'id'), $tableName));
        if ($count) {
            $result[0]['totalCount'] = $count;
        }

        return $result;
    }

    private function getStatsRankingGce(string $tableName, OpenChatApiArgs $args): array
    {
        $categoryStatement = $args->category ? "category = {$args->category}" : 1;

        $sort = [
            'rank' => 'id',
            'increase' => 'diff_member',
            'rate' => 'percent_increase',
        ];

        $sortColumn = $sort[$args->sort] ?? $sort['rate'];

        $params = [
            'offset' => $args->page * $args->limit,
            'limit' => $args->limit,
        ];

        $searchBan = getSeachBannedIdQuery('id');

        if (!$args->page) {
            // 1ページ目の場合
            $query = fn ($category) => fn ($where) =>
            "(
                SELECT
                    id
                FROM
                    open_chat
                {$where} AND {$category} AND NOT {$searchBan} AND {$tableName}_id > 0
                ORDER BY
                    {$tableName}_{$sortColumn} {$args->order}
                LIMIT
                    :offset, :limit
            )
            UNION ALL
            (
                SELECT
                    count(*) AS id
                FROM
                    open_chat
                {$where} AND {$category} AND NOT {$searchBan} AND {$tableName}_id > 0
            )";
        } else {
            $query = fn ($category) => fn ($where) =>
            "SELECT
                id
            FROM
                open_chat
            {$where} AND {$category} AND NOT {$searchBan} AND {$tableName}_id > 0
            ORDER BY
                {$tableName}_{$sortColumn} {$args->order}
            LIMIT
                :offset, :limit";
        }

        $args->keyword = preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $args->keyword);
        $args->keyword = preg_replace('/\s+/u', ' ', $args->keyword);

        $search = DBGce::executeFulltextSearchQuery(
            $query($categoryStatement),
            'MATCH(name, description) AGAINST(:search IN BOOLEAN MODE)',
            $args->keyword,
            $params,
        );

        // 1ページ目の場合
        if (!$args->page) {
            $count = array_pop($search);
            $count = $count['id'];
            return [$search, $count];
        }

        return [$search, 0];
    }

    private function getStatsRecord(array $idArray, string $tableName): array
    {
        $statements = array_fill(0, count($idArray), "?");
        $statement = "(" . implode(',', $statements) . ")";

        $query =
            "SELECT
                oc.id,
                oc.name,
                oc.description,
                oc.member,
                oc.img_url,
                oc.emblem,
                oc.category,
                sr.diff_member,
                sr.percent_increase
            FROM
                open_chat AS oc
                JOIN {$tableName} AS sr ON oc.id = sr.open_chat_id
            WHERE
                oc.id IN {$statement}";

        $stmt = DB::prepare($query);
        $stmt->execute($idArray);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    function findStatsAll(OpenChatApiArgs $args): array
    {
        [$search, $count] = $this->findStatsAllGce($args);
        if (!$search) {
            return [];
        }

        $result = $this->merge($search, $this->getRankingRecord(array_column($search, 'id')));
        if ($count) {
            $result[0]['totalCount'] = $count;
        }

        return array_map(fn ($oc) => new OpenChatListDto($oc), $result);
    }

    private function findStatsAllGce(OpenChatApiArgs $args): array
    {
        $categoryStatement = $args->category ? "category = {$args->category}" : 1;

        $sort = [
            'member' => "member {$args->order}, api_created_at DESC",
            'created_at' => "api_created_at {$args->order}",
        ];

        $where = [
            'member' => '',
            'created_at' => " AND api_created_at != ''",
        ];

        $sortColumn = $sort[$args->sort] ?? $sort['member'];
        $whereClause = $where[$args->sort] ?? $where['member'];

        $params = [
            'offset' => $args->page * $args->limit,
            'limit' => $args->limit,
        ];

        $searchBan = getSeachBannedIdQuery('id');

        if (!$args->page) {
            // 1ページ目の場合
            $query = fn ($category) => fn ($where) =>
            "(
                SELECT
                    id
                FROM
                    open_chat
                {$where} AND {$category} AND is_alive = 1 AND NOT {$searchBan}
                ORDER BY
                    {$sortColumn}
                LIMIT
                    :offset, :limit
            )
            UNION ALL
            (
                SELECT
                    count(*) AS id
                FROM
                    open_chat
                {$where} AND {$category} AND is_alive = 1 AND NOT {$searchBan}
            )";
        } else {
            $query = fn ($category) => fn ($where) =>
            "SELECT
                id
            FROM
                open_chat
            {$where} AND {$category} AND is_alive = 1 AND NOT {$searchBan}
            ORDER BY
                {$sortColumn}
            LIMIT
                :offset, :limit";
        }

        $args->keyword = preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $args->keyword);
        $args->keyword = preg_replace('/\s+/u', ' ', $args->keyword);

        $search = DBGce::executeFulltextSearchQuery(
            $query($categoryStatement . $whereClause),
            'WHERE MATCH(name, description) AGAINST(:search IN BOOLEAN MODE)',
            $args->keyword,
            $params,
        );

        // 1ページ目の場合
        if (!$args->page) {
            $count = array_pop($search);
            $count = $count['id'];
            return [$search, $count];
        }

        return [$search, 0];
    }

    private function getRankingRecord(array $idArray): array
    {
        $statements = array_fill(0, count($idArray), "?");
        $statement = "(" . implode(',', $statements) . ")";

        $query =
            "SELECT
                id,
                name,
                description,
                member,
                img_url,
                emblem,
                category,
                api_created_at
            FROM
                open_chat
            WHERE
                id IN {$statement}";

        $stmt = DB::prepare($query);
        $stmt->execute($idArray);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
