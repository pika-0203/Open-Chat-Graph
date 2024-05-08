<?php

declare(strict_types=1);

namespace App\Models\RecommendRepositories;

use Shadow\DB;

class RecommendRankingRepository implements RecommendRankingRepositoryInterface
{
    function getRanking(
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
                        t1.diff_member AS diff_member
                    FROM
                        (
                            SELECT
                                *
                            FROM
                                {$table}
                            WHERE
                                diff_member >= :minDiffMember
                        ) AS t1
                        JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                    WHERE
                        t2.tag = :tag
                ) AS ranking ON oc.id = ranking.id
            ORDER BY
                ranking.diff_member DESC
            LIMIT
                :limit",
            compact('tag', 'limit', 'minDiffMember')
        );
    }

    function getRankingByExceptId(
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
                        t1.diff_member AS diff_member
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
                        JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                    WHERE
                        t2.tag = :tag
                ) AS ranking ON oc.id = ranking.id
                LEFT JOIN statistics_ranking_hour AS rh ON rh.open_chat_id = oc.id
            WHERE
                oc.id NOT IN ({$ids})
            ORDER BY
                rh.diff_member DESC, ranking.diff_member DESC
            LIMIT
                :limit",
            compact('tag', 'limit', 'minDiffMember')
        );
    }

    function getListOrderByMemberDesc(
        string $tag,
        array $idArray,
        int $limit,
    ): array {
        $ids = implode(",", $idArray) ?: 0;
        $select = RecommendRankingRepositoryInterface::Select;
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
                        JOIN (
                            SELECT
                                *
                            FROM
                                recommend
                            WHERE
                                tag = :tag
                        ) AS ranking ON oc.id = ranking.id
                    WHERE
                        oc.id NOT IN ({$ids})
                        AND oc.member >= 15
                    ORDER BY
                        oc.member DESC
                    LIMIT
                        :limit
                ) AS t1
                LEFT JOIN statistics_ranking_hour24 AS t2 ON t1.id = t2.open_chat_id
            ORDER BY
                t2.diff_member DESC, t1.member DESC",
            compact('tag', 'limit')
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
