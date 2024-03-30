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
                        20
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
        $cate = $category ? "oc.category = {$category}" : "oc.category IS NULL";
        $table = $category ? 'statistics_ranking_hour' : "statistics_ranking_day";

        return DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.local_img_url AS img_url,
                oc.member
            FROM
                open_chat AS oc
                JOIN {$table} AS ranking ON oc.id = ranking.id
            WHERE
                {$cate}
                AND NOT oc.id = :id
            ORDER BY
                ranking.id ASC
            LIMIT
                 20",
            compact('id')
        );
    }

    function getRecommend(int $open_chat_id): array
    {
        $geneTag = fn ($s) => mb_strstr($s, '_OR_', true) ?: $s;

        $tag = DB::fetchColumn("SELECT tag FROM oc_tag WHERE id = {$open_chat_id}");
        if (!$tag) {
            $tag = $this->getRecommendTag($open_chat_id);
            $tag2 = false;
        } else {
            $tag2 = DB::fetchColumn("SELECT tag FROM oc_tag2 WHERE id = {$open_chat_id}");
            if (!$tag2) $tag2 = $this->getRecommendTag($open_chat_id);
            if ($tag2 === $tag) $tag2 = false;
        }

        if (!$tag) {
            $category = $this->getCategory($open_chat_id);
            $tag = $category ? array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category] : 'その他';
            $tag = "「{$tag}」カテゴリー";
            return [$this->getCategoryRanking($open_chat_id, $category), $tag, [], ''];
        }

        $r1 = $this->getRanking($open_chat_id, $tag);
        $tag = $geneTag($tag);
        $tag = "「{$tag}」タグ";

        if ($tag2) {
            $r2 = $this->getRanking($open_chat_id, $tag2);
            $tag2 = $geneTag($tag2);
            $tag2 = "「{$tag2}」タグ";
        } else {
            $category = $this->getCategory($open_chat_id);
            $r2 = $this->getCategoryRanking($open_chat_id, $category);
            $tag2 = $category ? array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category] : 'その他';
            $tag2 = "「{$tag2}」カテゴリー";
        }

        return [$r1, $tag, $r2, $tag2];
    }
}
