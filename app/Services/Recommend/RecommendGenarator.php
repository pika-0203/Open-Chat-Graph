<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Config\AppConfig;
use Shadow\DB;

class RecommendGenarator
{
    private const LIST_LIMIT = 25;
    private const MAX_LIST_LEN = 80;
    private const MAX_DIFF = 3;

    function getRankingTable(int $id, string $tag, string $table, int $limit)
    {
        $diff = self::MAX_DIFF;
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member,
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
                                AND diff_member >= {$diff}
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
            compact('tag', 'id', 'limit')
        );
    }

    function getRankingTableByExceptId(int $id, string $tag, array $idArray, string $table, int $limit)
    {
        $diff = self::MAX_DIFF;
        $ids = implode(",", $idArray) ?: 0;
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member,
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
                                        AND diff_member >= {$diff}
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
            compact('tag', 'id', 'limit')
        );
    }

    function getTagTableOrderByMember(int $id, string $tag, array $idArray, int $limit)
    {
        $ids = implode(",", $idArray) ?: 0;
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member,
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

    function getRanking(int $id, string $tag, bool $shuffle = true)
    {
        $limit = self::LIST_LIMIT;

        $ranking = $this->getRankingTable($id, $tag, 'statistics_ranking_hour', $limit);
        
        $idArray = array_column($ranking, 'id');
        $ranking2 = $this->getRankingTableByExceptId($id, $tag, $idArray, 'statistics_ranking_day', $limit);
        
        $idArray = array_column(array_merge($ranking, $ranking2), 'id');
        $ranking3 = $this->getRankingTableByExceptId($id, $tag, $idArray, 'statistics_ranking_week', $limit);
        
        $idArray = array_column(array_merge($ranking, $ranking2, $ranking3), 'id');
        $ranking4 = $this->getTagTableOrderByMember($id, $tag, $idArray, min(self::MAX_LIST_LEN - count($idArray), self::LIST_LIMIT));
        
        if($shuffle) {
            shuffle($ranking);
            shuffle($ranking2);
            shuffle($ranking3);
            shuffle($ranking4);
            $ranking5 = array_merge($ranking2, $ranking3);
            shuffle($ranking5);
            return array_merge($ranking, $ranking5, $ranking4);
        }
        
        return array_merge($ranking, $ranking2, $ranking3, $ranking4);
    }


    function getCategoryRankingTable(int $id, int $category, string $table, int $limit)
    {
        $diff = self::MAX_DIFF;
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member,
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
                        diff_member >= {$diff}
                ) AS ranking ON oc.id = ranking.open_chat_id
            ORDER BY
                ranking.id ASC
            LIMIT
                :limit",
            compact('id', 'category', 'limit')
        );
    }

    function getCategoryRankingTableByExceptId(int $id, int $category, array $idArray, string $table, int $limit)
    {
        $diff = self::MAX_DIFF;
        $ids = implode(",", $idArray) ?: 0;
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member,
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
                                diff_member >= {$diff}
                        ) AS sr1
                ) AS ranking ON oc.id = ranking.open_chat_id
            WHERE
                oc.id NOT IN ({$ids})
            ORDER BY
                ranking.id ASC
            LIMIT
                :limit",
            compact('id', 'category', 'limit')
        );
    }

    function getCategoryOrderByMember(int $id, int $category, array $idArray, int $limit)
    {
        $ids = implode(",", $idArray) ?: 0;
        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member,
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

    function getCategoryRanking(int $id, int $category, bool $shuffle = true)
    {
        $limit = self::LIST_LIMIT;

        $ranking = $this->getCategoryRankingTable($id, $category, 'statistics_ranking_hour', $limit);

        $idArray = array_column($ranking, 'id');
        $ranking2 = $this->getCategoryRankingTableByExceptId($id, $category, $idArray, 'statistics_ranking_day', $limit);

        $idArray = array_column(array_merge($ranking, $ranking2), 'id');
        $ranking3 = $this->getCategoryRankingTableByExceptId($id, $category, $idArray, 'statistics_ranking_week', $limit);

        $idArray = array_column(array_merge($ranking, $ranking2, $ranking3), 'id');
        $ranking4 = $this->getCategoryOrderByMember($id, $category, $idArray, min(self::MAX_LIST_LEN - count($idArray), self::LIST_LIMIT));

        if ($shuffle) {
            shuffle($ranking);
            shuffle($ranking2);
            shuffle($ranking3);
            shuffle($ranking4);
            $ranking5 = array_merge($ranking2, $ranking3);
            shuffle($ranking5);
            return array_merge($ranking, $ranking5, $ranking4);
        }

        return array_merge($ranking, $ranking2, $ranking3, $ranking4);
    }

    function getCategory(int $id)
    {
        return DB::fetchColumn("SELECT category FROM open_chat WHERE id = {$id}") ?? 0;
    }

    function getRecommendTag(int $id)
    {
        return DB::fetchColumn("SELECT tag FROM recommend WHERE id = {$id}");
    }

    function geneTag($s)
    {
        return str_replace('_AND_', ' ', mb_strstr($s, '_OR_', true) ?: $s);
    }

    function getRecommend(int $open_chat_id, bool $shuffle = true): array
    {
        $recommendTag = $this->getRecommendTag($open_chat_id);

        $tag = DB::fetchColumn("SELECT tag FROM oc_tag WHERE id = {$open_chat_id}");
        if (!$tag) {
            $tag = $recommendTag;
        }

        $tag2 = DB::fetchColumn("SELECT tag FROM oc_tag2 WHERE id = {$open_chat_id}");
        if (!$tag2) $tag2 = $recommendTag;
        if ($tag2 === $tag) $tag2 = false;

        if (!$tag2 && !$tag) {
            $category = $this->getCategory($open_chat_id);
            $tag = array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category];
            $tag = "「{$tag}」カテゴリー";
            return [$this->getCategoryRanking($open_chat_id, $category, $shuffle), $tag, [], ''];
        }

        $r1 = [];
        if ($tag) {
            $r1 = $this->getRanking($open_chat_id, $tag, $shuffle);
            $tag = $this->geneTag($tag);
            $tag = "「{$tag}」関連";
        }

        $r2 = [];
        if ($tag2) {
            $r2 = $this->getRanking($open_chat_id, $tag2, $shuffle);
            $tag2 = $this->geneTag($tag2);
            $tag2 = "「{$tag2}」関連";
            if (!$r1) {
                $category = $this->getCategory($open_chat_id);
                $r1 = $this->getCategoryRanking($open_chat_id, $category, $shuffle);
                $tag = array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category];
                $tag = "「{$tag}」カテゴリー";
                return [$r2, $tag2, $r1, $tag];
            }
            return [$r1, $tag, $r2, $tag2];
        }

        $category = $this->getCategory($open_chat_id);
        $r3 = $this->getCategoryRanking($open_chat_id, $category, $shuffle);
        $tag3 = array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category];
        $tag3 = "「{$tag3}」カテゴリー";
        return [$r1 ?: $r3, $r1 ? $tag : $tag3, $r1 ? $r3 : [], $r1 ? $tag3 : ''];
    }
}
