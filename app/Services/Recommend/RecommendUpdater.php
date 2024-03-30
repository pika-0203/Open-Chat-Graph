<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Config\AppConfig;
use Shadow\DB;

class RecommendUpdater
{
    const NAME_STRONG_TAG = [
        "ボイメ 歌",
        "ライブトーク",
        "イケボ",
        "カワボ",
        "独り言",
        "愚痴",
        "毒親",
        "恋愛",
        "自撮り_OR_顔出し",
        "宣伝",
        "ヒカマニ",
        "セミナー",
        "MBTI",
        "地雷系_OR_地雷_OR_量産型_OR_量産",
        "メンヘラ",
        "すとぷり",
        "レスバ_OR_アンチ_OR_喧嘩_OR_下あり_OR_下ネタ",
        "東方",
        "ちいかわ",
        "アオのハコ",
        "フリーレン",
        "V系_OR_ヴィジュアル系",
        "復縁",
        "不登校",
        "精神疾患_OR_精神障害",
        "発達障害_OR_ADHD_OR_ASD",
        "障害者",
        "ネッ友_OR_ネ友",
        "オリキャラ",
        "ゆるなり_OR_緩也",
        "なりきり_OR_全也_OR_nrkr_OR_全 也_OR_#也",
    ];

    const DESC_STRONG_TAG = [
        "V系_OR_ヴィジュアル系",
        "復縁",
        "不登校",
        "精神疾患_OR_精神障害",
        "発達障害_OR_ADHD_OR_ASD",
        "障害者",
        "ネッ友_OR_ネ友",
        "オリキャラ",
        "ゆるなり_OR_緩也",
        "なりきり_OR_全也_OR_nrkr_OR_全 也_OR_#也",
    ];

    /** @var string[] $tags */
    public array $tags;

    private function replace(string $str, string $column): string
    {
        $count = 0;
        $str3 = "{$column} LIKE '%" . str_replace(' ', "%' AND {$column} LIKE '%", $str, $count) . "%'";
        if ($count) return $str3;

        $str2 = "{$column} LIKE '%" . str_replace('_OR_', "%' OR {$column} LIKE '%", $str, $count) . "%'";
        return $count ? $str2 : $str3;
    }

    /** @return string[] */
    private function getReplacedTags(string $column): array
    {
        $this->tags = array_merge(
            self::NAME_STRONG_TAG,
            array_merge(...json_decode(
                file_get_contents(AppConfig::OPEN_CHAT_SUB_CATEGORIES_TAG_FILE_PATH),
                true
            ))
        );

        return array_map(fn ($str) => $this->replace($str, $column), $this->tags);
    }

    function updateName(string $column = 'oc.name', string $table = 'recommend')
    {
        $tags = $this->getReplacedTags($column);

        foreach ($tags as $key => $search) {
            $tag = $this->tags[$key];
            DB::execute(
                "INSERT IGNORE INTO
                    {$table}
                SELECT
                    oc.id,
                    '{$tag}'
                FROM
                    open_chat AS oc
                WHERE
                    {$search}"
            );
        }
    }

    /** @return array{ string:string[] }  */
    private function getReplacedTagsDesc(string $column): array
    {
        $this->tags = json_decode((file_get_contents(AppConfig::OPEN_CHAT_SUB_CATEGORIES_FILE_PATH)), true);

        return [
            array_map(fn ($a) => array_map(fn ($str) => $this->replace($str, $column), $a), $this->tags),
            array_map(fn ($str) => $this->replace($str, $column), self::DESC_STRONG_TAG)
        ];
    }

    function updateDescription(string $column = 'oc.description', string $table = 'recommend')
    {
        [$tags, $strongTags] = $this->getReplacedTagsDesc($column);

        $excute = fn ($table, $tag, $search, $category) => DB::execute(
            "INSERT IGNORE INTO
                {$table}
            SELECT
                oc.id,
                '{$tag}'
            FROM
                open_chat AS oc
            WHERE
                {$search}
                AND category = {$category}"
        );

        foreach ($tags as $category => $array) {
            foreach ($strongTags as $key => $search) {
                $tag = self::DESC_STRONG_TAG[$key];
                $excute($table, $tag, $search, $category);
            }

            foreach ($array as $key => $search) {
                $tag = $this->tags[$category][$key];
                $excute($table, $tag, $search, $category);
            }
        }
    }

    function updateName2(string $column = 'oc.name', string $table = 'oc_tag2')
    {
        $tags = $this->getReplacedTags($column);

        foreach ($tags as $key => $search) {
            $tag = $this->tags[$key];
            DB::execute(
                "INSERT IGNORE INTO
                    {$table}
                SELECT
                    oc.id,
                    '{$tag}'
                FROM
                    open_chat AS oc
                WHERE
                    {$search}
                    AND NOT EXISTS (
                        SELECT
                            id
                        FROM
                            oc_tag
                        WHERE
                            id = oc.id
                            AND tag = '{$tag}'        
                    )"
            );
        }
    }

    function updateDescription2(string $column = 'oc.description', string $table = 'oc_tag2')
    {
        [$tags, $strongTags] = $this->getReplacedTagsDesc($column);

        $excute = fn ($table, $tag, $search, $category) => DB::execute(
            "INSERT IGNORE INTO
                {$table}
            SELECT
                oc.id,
                '{$tag}'
            FROM
                open_chat AS oc
            WHERE
                {$search}
                AND category = {$category}
                AND NOT EXISTS (
                    SELECT
                        id
                    FROM
                        oc_tag
                    WHERE
                        id = oc.id
                        AND tag = '{$tag}'        
                )"
        );

        foreach ($tags as $category => $array) {
            foreach ($strongTags as $key => $search) {
                $tag = self::DESC_STRONG_TAG[$key];
                $excute($table, $tag, $search, $category);
            }

            foreach ($array as $key => $search) {
                $tag = $this->tags[$category][$key];
                $excute($table, $tag, $search, $category);
            }
        }
    }

    function updateRecommendTables()
    {
        DB::transaction(function () {
            DB::execute("DELETE FROM recommend");
            $this->updateDescription('oc.name', 'recommend');
            $this->updateName();
            $this->updateDescription();

            DB::execute("DELETE FROM oc_tag");
            $this->updateDescription('oc.name', 'oc_tag');
            $this->updateDescription(table: 'oc_tag');
            $this->updateName(table: 'oc_tag');
            $this->updateName('oc.description', 'oc_tag');

            DB::execute("DELETE FROM oc_tag2");
            $this->updateDescription2('oc.name');
            $this->updateDescription2();
            $this->updateName2();
            $this->updateName2('oc.description');
        });
    }
}
