<?php

declare(strict_types=1);

namespace App\Models\RecommendRepositories;

use Shadow\DB;

class OfficialRoomRankingRepository implements RecommendRankingRepositoryInterface
{
    function getRanking(
        string $emblem,
        string $table,
        int $minDiffMember,
        int $limit,
    ): array {
        $select = RecommendRankingRepositoryInterface::Select;

        if($emblem) {
            $statement = "emblem = '{$emblem}'";
        }else {
            $statement = "emblem = 1 OR emblem = 2";
        }

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
                        {$statement}
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
                ranking.diff_member DESC
            LIMIT
                :limit",
            compact('minDiffMember', 'limit')
        );
    }

    function getRankingByExceptId(
        string $emblem,
        string $table,
        int $minDiffMember,
        array $idArray,
        int $limit,
    ): array {
        $ids = implode(",", $idArray) ?: 0;
        $select = RecommendRankingRepositoryInterface::Select;

        if ($emblem) {
            $statement = "emblem = '{$emblem}'";
        } else {
            $statement = "emblem = 1 OR emblem = 2";
        }

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
                        {$statement}
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
                ranking.diff_member DESC
            LIMIT
                :limit",
            compact('minDiffMember', 'limit')
        );
    }

    function getListOrderByMemberDesc(
        string $emblem,
        array $idArray,
        int $limit
    ): array {
        $ids = implode(",", $idArray) ?: 0;
        $select = RecommendRankingRepositoryInterface::Select;

        if ($emblem) {
            $statement = "emblem = '{$emblem}'";
        } else {
            $statement = "emblem = 1 OR emblem = 2";
        }

        return DB::fetchAll(
            "SELECT
                {$select},
                'open_chat' AS table_name
            FROM
                open_chat AS oc
            WHERE
                {$statement}
                AND oc.id NOT IN ({$ids})
            ORDER BY
                oc.member DESC
            LIMIT
                :limit",
            compact('limit')
        );
    }

    function getCategory(int $id): int|null|false
    {
        return DB::fetchColumn("SELECT category FROM open_chat WHERE id = {$id}");
    }
}