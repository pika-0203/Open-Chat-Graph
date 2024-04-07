<?php

declare(strict_types=1);

namespace App\Models\RecommendRepositories;

use Shadow\DB;

class RecommendPageRepository implements RecommendRankingRepositoryInterface
{
    function getRanking(
        int $id,
        string $tag,
        string $table,
        int $minDiffMember,
        int $limit,
    ): array {
        $select = RecommendRankingRepositoryInterface::SelectPage;
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
                        t3.tag AS tag1,
                        t4.tag AS tag2
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
                        LEFT JOIN oc_tag AS t3 ON t1.open_chat_id = t3.id
                        LEFT JOIN oc_tag2 AS t4 ON t1.open_chat_id = t4.id
                    WHERE
                        t2.tag = :tag
                ) AS ranking ON oc.id = ranking.id
            ORDER BY
                ranking.diff_member DESC
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
        $select = RecommendRankingRepositoryInterface::SelectPage;
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
                        t3.tag AS tag1,
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
                                        open_chat_id != :id
                                        AND diff_member >= :minDiffMember
                                ) AS sr1
                        ) AS t1
                        JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                        LEFT JOIN oc_tag AS t3 ON t1.open_chat_id = t3.id
                        LEFT JOIN oc_tag2 AS t4 ON t1.open_chat_id = t4.id
                    WHERE
                        t2.tag = :tag
                ) AS ranking ON oc.id = ranking.id
            WHERE
                oc.id NOT IN ({$ids})
            ORDER BY
                ranking.diff_member DESC
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
        $select = RecommendRankingRepositoryInterface::SelectPage;
        return DB::fetchAll(
            "SELECT
                {$select},
                'open_chat' AS table_name
            FROM
                open_chat AS oc
                JOIN (
                    SELECT
                        r.*,
                        t3.tag AS tag1,
                        t4.tag AS tag2
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
                        LEFT JOIN oc_tag AS t3 ON r.id = t3.id
                        LEFT JOIN oc_tag2 AS t4 ON r.id = t4.id
                ) AS ranking ON oc.id = ranking.id
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