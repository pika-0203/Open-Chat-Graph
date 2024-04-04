<?php

declare(strict_types=1);

namespace App\Models\RecommendRepositories;

use Shadow\DB;

class RecommendRankingRepository implements RecommendRankingRepositoryInterface
{
    function getRanking(
        int $id,
        string $tag,
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
                open_chat AS oc
                JOIN (
                    SELECT
                        t2.id,
                        t1.id AS ranking_id
                    FROM
                        (
                            SELECT
                                *
                            FROM
                                {$table}
                            WHERE
                                open_chat_id != :id
                                AND diff_member >= :minDiffMember
                        ) AS t1
                        JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                    WHERE
                        t2.tag = :tag
                    ORDER BY
                        ranking_id ASC
                ) AS ranking ON oc.id = ranking.id
            ORDER BY
                ranking.ranking_id ASC
            LIMIT
                :limit",
            compact('tag', 'id', 'limit', 'minDiffMember')
        );
    }

    function getRankingByExceptId(
        int $id,
        string $tag,
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
                open_chat AS oc
                JOIN (
                    SELECT
                        t2.id,
                        t1.id AS ranking_id
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
                                        open_chat_id != :id
                                        AND diff_member >= :minDiffMember
                                ) AS sr1
                        ) AS t1
                        JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                    WHERE
                        t2.tag = :tag
                    ORDER BY
                        ranking_id ASC
                ) AS ranking ON oc.id = ranking.id
            WHERE
                oc.id NOT IN ({$ids})
            ORDER BY
                ranking.ranking_id ASC
            LIMIT
                :limit",
            compact('tag', 'id', 'limit', 'minDiffMember')
        );
    }

    function getListOrderByMemberDesc(
        int $id,
        string $tag,
        array $idArray,
        int $limit,
    ): array {
        $ids = implode(",", $idArray) ?: 0;
        $select = RecommendRankingRepositoryInterface::Select;
        return DB::fetchAll(
            "SELECT
                {$select},
                'open_chat' AS table_name
            FROM
                open_chat AS oc
                JOIN (
                    SELECT
                        r.*
                    FROM
                        (
                            SELECT
                                *
                            FROM
                                recommend
                            WHERE
                                tag = :tag
                                AND NOT id = :id
                        ) AS r
                ) AS reco ON oc.id = reco.id
            WHERE
                oc.id NOT IN ({$ids})
            ORDER BY
                oc.member DESC
            LIMIT
                :limit",
            compact('tag', 'id', 'limit')
        );
    }

    function getRecommendTag(int $id): string|false
    {
        return DB::fetchColumn("SELECT tag FROM recommend WHERE id = {$id}");
    }

    /** @return array{0:string|false,1:string|false} */
    function getTags(int $id): array
    {
        $tag = DB::fetchColumn("SELECT tag FROM oc_tag WHERE id = {$id}");
        $tag2 = DB::fetchColumn("SELECT tag FROM oc_tag2 WHERE id = {$id}");
        return [$tag, $tag2];
    }
}
