<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Config\AppConfig;
use Shadow\DB;

class RecommendGenarator
{
    private const LIST_LIMIT = 50;

    function getRanking(int $id, string $tag)
    {
        $limit = self::LIST_LIMIT;
        $ranking = $this->getRankingTable($id, $tag, $limit, 'statistics_ranking_hour');
        shuffle($ranking);
        $count = count($ranking);
        if ($count >= $limit) return $ranking;

        $ranking = array_merge($ranking, $this->getRankingTableByExceptTable(
            $id,
            $tag,
            $limit - $count,
            'statistics_ranking_day',
            'statistics_ranking_hour'
        ));
        shuffle($ranking);
        $count = count($ranking);
        if ($count >= $limit) return $ranking;

        $week = $this->getRankingTableByExceptTable2(
            $id,
            $tag,
            $limit - $count,
            'statistics_ranking_week',
            'statistics_ranking_day',
            'statistics_ranking_hour',
        );
        shuffle($week);

        $ranking = array_merge($ranking, $week);
        //shuffle($ranking);
        $count = count($ranking);
        if ($count >= $limit) return $ranking;

        $member = $this->getTagTableOrderByMember(
            $id,
            $tag,
            $limit - $count,
        );
        shuffle($member);
        $ranking = array_merge($ranking, $member);
        return $ranking;
    }

    function getRankingTable(int $id, string $tag, int $limit, string $table)
    {
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member
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
                                AND diff_member >= 2
                        ) AS t1
                        JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                    WHERE
                        t2.tag = :tag
                    ORDER BY
                        ranking_id ASC
                    LIMIT
                        :limit
                ) AS ranking ON oc.id = ranking.id
            ORDER BY
                ranking.ranking_id ASC",
            compact('tag', 'id', 'limit')
        );
    }

    function getRankingTableByExceptTable(int $id, string $tag, int $limit, string $table, string $exceptTable)
    {
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member
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
                                        AND diff_member >= 2
                                ) AS sr1
                                LEFT JOIN (
                                    SELECT
                                        id,
                                        open_chat_id
                                    FROM
                                        {$exceptTable}
                                    WHERE
                                        open_chat_id != :id
                                        AND diff_member >= 2
                                ) AS sr2 ON sr1.open_chat_id = sr2.open_chat_id
                            WHERE
                                sr2.id IS NULL
                        ) AS t1
                        JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                    WHERE
                        t2.tag = :tag
                    ORDER BY
                        ranking_id ASC
                    LIMIT
                        :limit
                ) AS ranking ON oc.id = ranking.id
            ORDER BY
                ranking.ranking_id ASC",
            compact('tag', 'id', 'limit')
        );
    }

    function getRankingTableByExceptTable2(
        int $id,
        string $tag,
        int $limit,
        string $table,
        string $exceptTable,
        string $exceptTable2
    ) {
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member
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
                                        AND diff_member >= 2
                                ) AS sr1
                                LEFT JOIN (
                                    SELECT
                                        id,
                                        open_chat_id
                                    FROM
                                        {$exceptTable}
                                    WHERE
                                        open_chat_id != :id
                                        AND diff_member >= 2
                                ) AS sr2 ON sr1.open_chat_id = sr2.open_chat_id
                                LEFT JOIN (
                                    SELECT
                                        id,
                                        open_chat_id
                                    FROM
                                        {$exceptTable2}
                                    WHERE
                                        open_chat_id != :id
                                        AND diff_member >= 2
                                ) AS sr3 ON sr1.open_chat_id = sr3.open_chat_id
                            WHERE
                                sr2.id IS NULL
                        ) AS t1
                        JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                    WHERE
                        t2.tag = :tag
                    ORDER BY
                        ranking_id ASC
                    LIMIT
                        :limit
                ) AS ranking ON oc.id = ranking.id
            ORDER BY
                ranking.ranking_id ASC",
            compact('tag', 'id', 'limit')
        );
    }


    function getTagTableOrderByMember(int $id, string $tag, int $limit)
    {
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member
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
                        LEFT JOIN (
                            SELECT
                                *
                            FROM
                                statistics_ranking_hour
                            WHERE
                                diff_member >= 2
                        ) AS st1 ON r.id = st1.open_chat_id
                        LEFT JOIN (
                            SELECT
                                *
                            FROM
                                statistics_ranking_day
                            WHERE
                                diff_member >= 2
                        ) AS st2 ON r.id = st2.open_chat_id
                        LEFT JOIN (
                            SELECT
                                *
                            FROM
                                statistics_ranking_week
                            WHERE
                                diff_member >= 2
                        ) AS st3 ON r.id = st3.open_chat_id
                    WHERE
                        st1.id IS NULL
                        AND st2.id IS NULL
                        AND st3.id IS NULL
                ) AS reco ON oc.id = reco.id
            ORDER BY
                oc.member DESC
            LIMIT
                :limit",
            compact('tag', 'id', 'limit')
        );
    }
    function getCategory(int $id)
    {
        return DB::fetchColumn("SELECT category FROM open_chat WHERE id = {$id}") ?? 0;
    }

    function getRecommendTag(int $id)
    {
        return DB::fetchColumn("SELECT tag FROM recommend WHERE id = {$id}");
    }

    function getCategoryRanking(int $id, int $category)
    {
        if (!$category) return [];

        $limit = self::LIST_LIMIT;
        $ranking = $this->getCategoryRankingTable($id, $category, $limit, 'statistics_ranking_hour');
        shuffle($ranking);
        $count = count($ranking);
        if ($count >= $limit) return $ranking;

        $ranking = array_merge($ranking, $this->getCategoryRankingTableByExceptTable(
            $id,
            $category,
            $limit - $count,
            'statistics_ranking_day',
            'statistics_ranking_hour'
        ));
        shuffle($ranking);
        $count = count($ranking);
        if ($count >= $limit) return $ranking;

        $week = $this->getCategoryRankingTableByExceptTable2(
            $id,
            $category,
            $limit - $count,
            'statistics_ranking_week',
            'statistics_ranking_day',
            'statistics_ranking_hour',
        );
        shuffle($week);

        $ranking = array_merge($ranking, $week);
        $count = count($ranking);
        //shuffle($ranking);
        if ($count >= $limit) return $ranking;

        $member = $this->getCategoryOrderByMember(
            $id,
            $category,
            $limit - $count,
        );
        shuffle($member);
        $ranking = array_merge($ranking, $member);
        return $ranking;
    }

    function getCategoryRankingTable(int $id, int $category, int $limit, string $table)
    {
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member
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
                        diff_member >= 2
                    ORDER BY
                        id ASC
                    LIMIT
                        :limit
                ) AS ranking ON oc.id = ranking.open_chat_id
            ORDER BY
                ranking.id ASC",
            compact('id', 'category', 'limit')
        );
    }

    function getCategoryRankingTableByExceptTable(int $id, int $category, int $limit, string $table, string $exceptTable)
    {
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member
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
                                diff_member >= 2
                        ) AS sr1
                        LEFT JOIN (
                            SELECT
                                id,
                                open_chat_id
                            FROM
                                {$exceptTable}
                            WHERE
                                diff_member >= 2
                        ) AS sr2 ON sr1.open_chat_id = sr2.open_chat_id
                    WHERE
                        sr2.id IS NULL
                    ORDER BY
                        sr1.id ASC
                    LIMIT
                        :limit
                ) AS ranking ON oc.id = ranking.open_chat_id
            ORDER BY
                ranking.id ASC",
            compact('id', 'category', 'limit')
        );
    }

    function getCategoryRankingTableByExceptTable2(
        int $id,
        int $category,
        int $limit,
        string $table,
        string $exceptTable,
        string $exceptTable2
    ) {
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member
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
                                diff_member >= 2
                        ) AS sr1
                        LEFT JOIN (
                            SELECT
                                id,
                                open_chat_id
                            FROM
                                {$exceptTable}
                            WHERE
                                diff_member >= 2
                        ) AS sr2 ON sr1.open_chat_id = sr2.open_chat_id
                        LEFT JOIN (
                            SELECT
                                id,
                                open_chat_id
                            FROM
                                {$exceptTable2}
                            WHERE
                                diff_member >= 2
                        ) AS sr3 ON sr1.open_chat_id = sr3.open_chat_id
                    WHERE
                        sr2.id IS NULL
                        AND sr3.id IS NULL
                    ORDER BY
                        sr1.id ASC
                    LIMIT
                        :limit
                ) AS ranking ON oc.id = ranking.open_chat_id
            ORDER BY
                ranking.id ASC",
            compact('id', 'category', 'limit')
        );
    }


    function getCategoryOrderByMember(int $id, int $category, int $limit)
    {
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member
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
                LEFT JOIN (
                    SELECT
                        *
                    FROM
                        statistics_ranking_hour
                    WHERE
                        diff_member >= 2
                ) AS st1 ON oc.id = st1.open_chat_id
                LEFT JOIN (
                    SELECT
                        *
                    FROM
                        statistics_ranking_day
                    WHERE
                        diff_member >= 2
                ) AS st2 ON oc.id = st2.open_chat_id
                LEFT JOIN (
                    SELECT
                        *
                    FROM
                        statistics_ranking_week
                    WHERE
                        diff_member >= 2
                ) AS st3 ON oc.id = st3.open_chat_id
            WHERE
                st1.id IS NULL
                AND st2.id IS NULL
                AND st3.id IS NULL
            ORDER BY
                oc.member DESC
            LIMIT
                :limit",
            compact('id', 'category', 'limit')
        );
    }

    function getRecommend(int $open_chat_id): array
    {
        $geneTag = fn ($s) => str_replace('_AND_', ' ', mb_strstr($s, '_OR_', true) ?: $s);

        $tag = DB::fetchColumn("SELECT tag FROM oc_tag WHERE id = {$open_chat_id}");
        if (!$tag) {
            $tag = $this->getRecommendTag($open_chat_id);
        }

        $tag2 = DB::fetchColumn("SELECT tag FROM oc_tag2 WHERE id = {$open_chat_id}");
        if (!$tag2) $tag2 = $this->getRecommendTag($open_chat_id);
        if ($tag2 === $tag) $tag2 = false;

        if (!$tag2 && !$tag) {
            $category = $this->getCategory($open_chat_id);
            $tag = array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category];
            $tag = "「{$tag}」カテゴリー";
            return [$this->getCategoryRanking($open_chat_id, $category), $tag, [], ''];
        }

        $r1 = [];
        if ($tag) {
            $r1 = $this->getRanking($open_chat_id, $tag);
            $tag = $geneTag($tag);
            $tag = "「{$tag}」タグ";
        }

        $r2 = [];
        if ($tag2) {
            $r2 = $this->getRanking($open_chat_id, $tag2);
            $tag2 = $geneTag($tag2);
            $tag2 = "「{$tag2}」タグ";
            if (!$r1) {
                $category = $this->getCategory($open_chat_id);
                $r1 = $this->getCategoryRanking($open_chat_id, $category);
                $tag = array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category];
                $tag = "「{$tag}」カテゴリー";
                return [$r2, $tag2, $r1, $tag];
            }
            return [$r1, $tag, $r2, $tag2];
        }

        $category = $this->getCategory($open_chat_id);
        $r3 = $this->getCategoryRanking($open_chat_id, $category);
        $tag3 = array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category];
        $tag3 = "「{$tag3}」カテゴリー";
        return [$r1 ?: $r3, $r1 ? $tag : $tag3, $r1 ? $r3 : [], $r1 ? $tag3 : ''];
    }
}
