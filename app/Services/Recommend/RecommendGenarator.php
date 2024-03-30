<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Config\AppConfig;
use Shadow\DB;

class RecommendGenarator
{
    function getRanking(int $id, string $tag, string $table = 'recommend')
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
                        statistics_ranking_hour AS t1
                        JOIN {$table} AS t2 ON t1.open_chat_id = t2.id
                    WHERE
                        t2.tag = :tag
                        AND t2.id != :id
                    ORDER BY
                        ranking_id ASC
                    LIMIT
                        50
                ) AS ranking ON oc.id = ranking.id
            ORDER BY
                ranking.ranking_id ASC",
            compact('tag', 'id')
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
        return $category ? DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member
            FROM
                open_chat AS oc
                JOIN statistics_ranking_hour AS ranking ON oc.id = ranking.open_chat_id
            WHERE
                oc.category = :category
                AND NOT oc.id = :id
            ORDER BY
                ranking.id ASC
            LIMIT
                 50",
            compact('id', 'category')
        ) : [];
    }

    function getRecommend(int $open_chat_id): array
    {
        $geneTag = fn ($s) => mb_strstr($s, '_OR_', true) ?: $s;

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

        if (!$r1) {
            $category = $this->getCategory($open_chat_id);
            $r1 = $this->getCategoryRanking($open_chat_id, $category);
            $tag = array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category];
            $tag = "「{$tag}」カテゴリー";
        }

        return [$r1, $tag, $r2 ?: [], $tag2 ?: ''];
    }
}
