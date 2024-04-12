<?php

declare(strict_types=1);

namespace App\Models\ApiRepositories;

use Shadow\DB;

class OpenChatStatsRankingApiRepository
{
    function findHourlyStatsRanking(OpenChatApiArgs $args): array
    {
        return array_map(
            fn ($oc) => new OpenChatListDto($oc),
            $this->getStatsRanking('statistics_ranking_hour', $args)
        );
    }

    function findDailyStatsRanking(OpenChatApiArgs $args): array
    {
        return array_map(
            fn ($oc) => new OpenChatListDto($oc),
            $this->getStatsRanking('statistics_ranking_hour24', $args)
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
        $sort = [
            'rank' => 'sr.id',
            'increase' => 'sr.diff_member',
            'rate' => 'sr.percent_increase',
        ];

        $sortColumn = $sort[$args->sort] ?? $sort['rate'];

        $params = [
            'offset' => $args->page * $args->limit,
            'limit' => $args->limit,
        ];

        $query = fn ($category) => fn ($where) =>
        "SELECT
            oc.id,
            oc.name,
            oc.description,
            oc.member,
            oc.local_img_url AS img_url,
            oc.emblem,
            oc.category,
            sr.diff_member,
            sr.percent_increase
        FROM
            open_chat AS oc
            JOIN {$tableName} AS sr ON oc.id = sr.open_chat_id
        {$where} {$category}
        ORDER BY
            {$sortColumn} {$args->order}
        LIMIT
            :offset, :limit";

        $countQuery = fn ($category) => fn ($where) =>
        "SELECT
            count(*) as count
        FROM
            open_chat AS oc
            JOIN {$tableName} AS sr ON oc.id = sr.open_chat_id
        {$where} {$category}";

        $categoryStatement = $args->category ? "category = {$args->category}" : 1;

        // 検索が選択されていない場合
        if (!$args->sub_category && !$args->keyword) {
            $result = DB::fetchAll(
                $query($categoryStatement)('WHERE'),
                $params
            );

            if (!$result || $args->page !== 0) {
                return $result;
            }

            // 1ページ目の場合は件数を含める
            $result[0]['totalCount'] = DB::fetchColumn($countQuery($categoryStatement)('WHERE'));
            return $result;
        }

        // サブカテゴリー選択時
        if ($args->sub_category) {
            $result = DB::executeLikeSearchQuery(
                $query("AND " . $categoryStatement),
                fn ($i) => "(oc.name LIKE :keyword{$i} OR oc.description LIKE :keyword{$i})",
                $args->sub_category,
                $params
            );

            if (!$result || $args->page !== 0) {
                return $result;
            }

            $count = DB::executeLikeSearchQuery(
                $countQuery("AND " . $categoryStatement),
                fn ($i) => "(oc.name LIKE :keyword{$i} OR oc.description LIKE :keyword{$i})",
                $args->sub_category
            );

            $result[0]['totalCount'] = $count[0]['count'];

            return $result;
        }

        // キーワード検索時
        $result = DB::executeLikeSearchQuery(
            $query("AND " . $categoryStatement),
            fn ($i) => "(oc.name LIKE :keyword{$i} OR oc.description LIKE :keyword{$i})",
            $args->keyword,
            $params
        );

        if (!$result || $args->page !== 0) {
            return $result;
        }

        $count = DB::executeLikeSearchQuery(
            $countQuery("AND " . $categoryStatement),
            fn ($i) => "(oc.name LIKE :keyword{$i} OR oc.description LIKE :keyword{$i})",
            $args->keyword
        );

        $result[0]['totalCount'] = $count[0]['count'];
        return $result;
    }

    function findStatsAll(OpenChatApiArgs $args): array
    {
        $sort = [
            'member' => 'oc.member',
            'created_at' => 'oc.api_created_at',
        ];

        $where = [
            'member' => '',
            'created_at' => " AND oc.api_created_at IS NOT NULL AND oc.api_created_at != ''",
        ];

        $sortColumn = $sort[$args->sort] ?? $sort['member'];
        $whereClause = $where[$args->sort] ?? $where['member'];

        $params = [
            'offset' => $args->page * $args->limit,
            'limit' => $args->limit,
        ];

        $query = fn ($category) => fn ($where) =>
        "SELECT
            oc.id,
            oc.name,
            oc.description,
            oc.member,
            oc.local_img_url AS img_url,
            oc.emblem,
            oc.category,
            oc.api_created_at
        FROM
            open_chat AS oc
        {$where} {$category}
        ORDER BY
            {$sortColumn} {$args->order}
        LIMIT
            :offset, :limit";

        $countQuery = fn ($category) => fn ($where) =>
        "SELECT
            count(*) as count
        FROM
            open_chat AS oc
        {$where} {$category}";

        $categoryStatement = $args->category ? "category = {$args->category}" : 1;

        // サブカテゴリーが選択されていない場合
        if (!$args->sub_category && !$args->keyword) {
            $result = array_map(
                fn ($oc) => new OpenChatListDto($oc),
                DB::fetchAll(
                    $query($categoryStatement . $whereClause)('WHERE'),
                    $params
                )
            );

            if (!$result || $args->page !== 0) {
                return $result;
            }

            // 1ページ目の場合は件数を含める
            $result[0]->totalCount = DB::fetchColumn($countQuery($categoryStatement . $whereClause)('WHERE'));
            return $result;
        }

        // サブカテゴリー選択時
        if ($args->sub_category) {
            $result = array_map(
                fn ($oc) => new OpenChatListDto($oc),
                DB::executeLikeSearchQuery(
                    $query("AND " . $categoryStatement . $whereClause),
                    fn ($i) => "(oc.name LIKE :keyword{$i} OR oc.description LIKE :keyword{$i})",
                    $args->sub_category,
                    $params
                )
            );

            if (!$result || $args->page !== 0) {
                return $result;
            }

            $count = DB::executeLikeSearchQuery(
                $countQuery("AND " . $categoryStatement . $whereClause),
                fn ($i) => "(oc.name LIKE :keyword{$i} OR oc.description LIKE :keyword{$i})",
                $args->sub_category
            );

            $result[0]->totalCount = $count[0]['count'];
            return $result;
        }

        // キーワード検索時
        $result = array_map(
            fn ($oc) => new OpenChatListDto($oc),
            DB::executeLikeSearchQuery(
                $query("AND " . $categoryStatement . $whereClause),
                fn ($i) => "(oc.name LIKE :keyword{$i} OR oc.description LIKE :keyword{$i})",
                $args->keyword,
                $params
            )
        );

        if (!$result || $args->page !== 0) {
            return $result;
        }

        $count = DB::executeLikeSearchQuery(
            $countQuery("AND " . $categoryStatement . $whereClause),
            fn ($i) => "(oc.name LIKE :keyword{$i} OR oc.description LIKE :keyword{$i})",
            $args->keyword
        );

        $result[0]->totalCount = $count[0]['count'];
        return $result;
    }
}
