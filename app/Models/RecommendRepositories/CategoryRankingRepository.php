<?php

declare(strict_types=1);

namespace App\Models\RecommendRepositories;

use Shadow\DB;

class CategoryRankingRepository implements RecommendRankingRepositoryInterface
{
    function getRanking(
        int $id,
        string $category,
        string $table,
        int $minDiffMember,
        int $limit,
    ): array {
        $select = RecommendRankingRepositoryInterface::Select;
        return DB::fetchAll(
            "SELECT
                {$select},
                '{$table}' AS table_name
            FROM
                (
                    SELECT
                        *
                    FROM
                        open_chat
                    WHERE
                        category = :category
                        AND NOT id = :id
                ) AS oc
                JOIN (
                    SELECT
                        *
                    FROM
                        {$table}
                    WHERE
                        diff_member >= :minDiffMember
                ) AS ranking ON oc.id = ranking.open_chat_id
            ORDER BY
                ranking.id ASC
            LIMIT
                :limit",
            compact('id', 'category', 'minDiffMember', 'limit')
        );
    }

    function getRankingByExceptId(
        int $id,
        string $category,
        string $table,
        int $minDiffMember,
        array $idArray,
        int $limit,
    ): array {
        $ids = implode(",", $idArray) ?: 0;
        $select = RecommendRankingRepositoryInterface::Select;
        return DB::fetchAll(
            "SELECT
                {$select},
                '{$table}' AS table_name
            FROM
                (
                    SELECT
                        *
                    FROM
                        open_chat
                    WHERE
                        category = :category
                        AND NOT id = :id
                ) AS oc
                JOIN (
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
                ) AS ranking ON oc.id = ranking.open_chat_id
            WHERE
                oc.id NOT IN ({$ids})
            ORDER BY
                ranking.id ASC
            LIMIT
                :limit",
            compact('id', 'category', 'minDiffMember', 'limit')
        );
    }

    function getListOrderByMemberDesc(
        int $id,
        string $category,
        array $idArray,
        int $limit
    ): array {
        $ids = implode(",", $idArray) ?: 0;
        $select = RecommendRankingRepositoryInterface::Select;
        return DB::fetchAll(
            "SELECT
                {$select},
                'open_chat' AS table_name
            FROM
                open_chat AS oc
            WHERE
                oc.category = :category
                AND oc.id NOT IN ({$ids})
                AND NOT oc.id = :id
            ORDER BY
                oc.member DESC
            LIMIT
                :limit",
            compact('id', 'category', 'limit')
        );
    }

    function getCategory(int $id): int|null
    {
        return DB::fetchColumn("SELECT category FROM open_chat WHERE id = {$id}");
    }
}
