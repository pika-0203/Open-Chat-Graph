<?php

declare(strict_types=1);

namespace App\Models\RecommendRepositories;

use App\Models\Repositories\DB;

class CategoryRankingRepository extends AbstractRecommendRankingRepository
{
    function getRanking(
        string $category,
        string $table,
        int $minDiffMember,
        int $limit,
    ): array {
        $select = self::SelectPage;
        return DB::fetchAll(
            "SELECT
                {$select},
                '{$table}' AS table_name
            FROM
                open_chat AS oc
                JOIN (
                    SELECT
                        t2.id,
                        t1.diff_member AS diff_member,
                        t2.tag AS tag1,
                        t4.tag AS tag2
                    FROM
                        (
                            SELECT
                                *
                            FROM
                                {$table}
                            WHERE
                                diff_member >= :minDiffMember
                        ) AS t1
                        LEFT JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                        LEFT JOIN oc_tag2 AS t4 ON t1.open_chat_id = t4.id
                ) AS ranking ON oc.id = ranking.id
            WHERE
                oc.category = :category
            ORDER BY
                ranking.diff_member DESC
            LIMIT
                :limit",
            compact('category', 'limit', 'minDiffMember')
        );
    }

    function getRankingByExceptId(
        string $category,
        string $table,
        int $minDiffMember,
        array $idArray,
        int $limit,
    ): array {
        $ids = implode(",", $idArray) ?: 0;
        $select = self::SelectPage;
        return DB::fetchAll(
            "SELECT
                {$select},
                '{$table}' AS table_name
            FROM
                open_chat AS oc
                JOIN (
                    SELECT
                        t2.id,
                        t1.diff_member AS diff_member,
                        t2.tag AS tag1,
                        t4.tag AS tag2
                    FROM
                        (
                            SELECT
                                sr1.*
                            FROM
                                (
                                    SELECT
                                        *
                                    FROM
                                        {$table}
                                    WHERE
                                        diff_member >= :minDiffMember
                                ) AS sr1
                        ) AS t1
                        LEFT JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                        LEFT JOIN oc_tag2 AS t4 ON t1.open_chat_id = t4.id
                ) AS ranking ON oc.id = ranking.id
                LEFT JOIN statistics_ranking_hour AS rh ON rh.open_chat_id = oc.id
            WHERE
                oc.id NOT IN ({$ids})
                AND oc.category = :category
            ORDER BY
                rh.diff_member DESC, ranking.diff_member DESC
            LIMIT
                :limit",
            compact('category', 'limit', 'minDiffMember')
        );
    }

    function getListOrderByMemberDesc(
        string $category,
        array $idArray,
        int $limit
    ): array {
        $ids = implode(",", $idArray) ?: 0;
        $select = self::SelectPage;
        return DB::fetchAll(
            "SELECT
                t1.*
            FROM
                (
                    SELECT
                        {$select},
                        'open_chat' AS table_name
                    FROM
                        open_chat AS oc
                        LEFT JOIN (
                            SELECT
                                r.id,
                                r.tag AS tag1,
                                t4.tag AS tag2
                            FROM
                                recommend AS r
                                LEFT JOIN oc_tag2 AS t4 ON r.id = t4.id
                        ) AS ranking ON oc.id = ranking.id
                        LEFT JOIN statistics_ranking_hour24 AS rh ON oc.id = rh.open_chat_id
                        LEFT JOIN statistics_ranking_hour AS rh2 ON oc.id = rh2.open_chat_id
                    WHERE
                        oc.id NOT IN ({$ids})
                        AND ((rh.open_chat_id IS NOT NULL OR rh2.open_chat_id IS NOT NULL) OR oc.member >= 15)
                        AND oc.category = :category
                    ORDER BY
                        oc.member DESC
                    LIMIT
                        :limit
                ) AS t1
                LEFT JOIN statistics_ranking_hour AS t2 ON t1.id = t2.open_chat_id
            ORDER BY
                t2.diff_member DESC, t1.member DESC",
            compact('category', 'limit')
        );
    }

    function getCategory(int $id): int|null|false
    {
        return DB::fetchColumn("SELECT category FROM open_chat WHERE id = {$id}");
    }
}
