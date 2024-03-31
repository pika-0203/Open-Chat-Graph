<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Config\AppConfig;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use Shadow\DB;

class RecommendUpdater
{
    const NAME_STRONG_TAG = [
        "なりきり_OR_全也_OR_nrkr_OR_#也_OR_D也_OR_ゆるなり_OR_緩也_OR_夢也_OR_夢 也",
        "IT資格_OR_基本情報技術者_OR_応用情報技術者_OR_ITパスポート_OR_情報処理試験_OR_ITストラテジ",
        "新入生同士の情報交換_OR_年度入学_OR_年度新入生_OR_新入生同士",
        "就活情報_OR_就活生情報_OR_選考対策・企業研究_OR_就活選考対策_OR_就活対策グループ_OR_就活テスト対策グループ",
        "ポイ活",
        "BLTトレードシステムサポートオプチャ",
        "スタートアップ_OR_ベンチャー",
        "オリキャラ恋愛_OR_折恋_OR_折 恋",
        "バウンティラッシュ_OR_バウンティ",
        "スピリチュアル",
        "占い師",
        "占い_OR_霊視",
        "ボイメ_AND_歌",
        "ヒカマニ",
        "イケボ",
        "ライブトーク",
        "独り言",
        "カラオケ",
        "セミナー",
        "SNS",
        "インスタ",
        "MBTI_OR_ISTJ_OR_ISFJ_OR_INFJ_OR_INTJ_OR_ISTP_OR_ISFP_OR_INFP_OR_INTP_OR_ESTP_OR_ESFP_OR_ENFP_OR_ENTP_OR_ESTJ_OR_ESFJ_OR_ENFJ_OR_ENTJ",
        "ぷにぷに",
        "地雷系_OR_地雷_OR_量産型_OR_量産",
        "メンヘラ",
        "すとぷり",
        "コレコレ_OR_コレリス",
        "カラフルピーチ_OR_からぴち_OR_カラピチ_OR_からピチ",
        "ホロライブ",
        "にじさんじ",
        "東方",
        "ちいかわ",
        "アオのハコ",
        "フリーレン",
        "ブルーロック",
        "ラブライブ_OR_ラブライバー",
        "不登校",
        "精神疾患_OR_精神障害",
        "知的障害_OR_境界知能",
        "発達障害_OR_ADHD_OR_ASD",
        "障害者",
        "ネッ友_OR_ネ友",
        "ゲイ_AND_バイ",
        "K也_OR_🇰🇷 也_OR_𝐊 也",
        "ChatGPT_OR_チャットGPT",
        "28卒",
        "27卒",
        "26卒",
        "25卒",
        "24卒",
        "23卒",
        "PTA_OR_ＰＴＡ",
        "パチスロ_OR_パチンコ_OR_スロット",
        "オリキャラ",
        "レスバ_OR_アンチ_OR_喧嘩_OR_下あり_OR_下ネタ",
        "復縁",
        "愚痴",
        "毒親",
        "恋愛",
        "宣伝",
        "太鼓の達人",
    ];

    const DESC_STRONG_TAG = [
        "unistyle",
        "jobhunt",
        "にじさんじ",
        "新入生同士の情報交換_OR_年度入学_OR_年度新入生_OR_新入生同士",
        "春から_AND_大学",
        "ポイ活",
        "ゲイ_AND_バイ",
        "復縁",
        "不登校",
        "知的障害_OR_境界知能",
        "精神疾患_OR_精神障害",
        "発達障害_OR_ADHD_OR_ASD",
        "障害者",
        "オリキャラ恋愛_OR_折恋_OR_折 恋",
        "K也_OR_🇰🇷 也_OR_𝐊 也",
        "ネッ友_OR_ネ友",
        "MBTI_OR_ISTJ_OR_ISFJ_OR_INFJ_OR_INTJ_OR_ISTP_OR_ISFP_OR_INFP_OR_INTP_OR_ESTP_OR_ESFP_OR_ENFP_OR_ENTP_OR_ESTJ_OR_ESFJ_OR_ENFJ_OR_ENTJ",
    ];

    /** @var string[] $tags */
    public array $tags;
    private string $start;
    private string $end;

    private function replace(string $str, string $column): string
    {
        $count = 0;
        $column = "{$column} COLLATE utf8mb4_general_ci";
        $str3 = "{$column} LIKE '%" . str_replace('_AND_', "%' AND {$column} LIKE '%", $str, $count) . "%'";
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
                    (SELECT * FROM open_chat WHERE updated_at BETWEEN :start AND :end) AS oc
                WHERE
                    {$search}",
                ['start' => $this->start, 'end' => $this->end]
            );
        }
    }

    /** @return array{ string:string[] }  */
    private function getReplacedTagsDesc(string $column): array
    {
        $this->tags = json_decode((file_get_contents(AppConfig::OPEN_CHAT_SUB_CATEGORIES_TAG_FILE_PATH)), true);

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
                (SELECT * FROM open_chat WHERE category = {$category} AND updated_at BETWEEN :start AND :end) AS oc
            WHERE
                {$search}",
            ['start' => $this->start, 'end' => $this->end]
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
                    (SELECT * FROM open_chat WHERE updated_at BETWEEN :start AND :end) AS oc
                WHERE
                    ({$search})
                    AND NOT EXISTS (
                        SELECT
                            id
                        FROM
                            oc_tag
                        WHERE
                            id = oc.id
                            AND tag COLLATE utf8mb4_general_ci = '{$tag}'
                    )",
                ['start' => $this->start, 'end' => $this->end]
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
                (SELECT * FROM open_chat WHERE category = {$category} AND updated_at BETWEEN :start AND :end) AS oc
            WHERE
                ({$search})
                AND NOT EXISTS (
                    SELECT
                        id
                    FROM
                        oc_tag
                    WHERE
                        id = oc.id
                        AND tag COLLATE utf8mb4_general_ci = '{$tag}' 
                )",
            ['start' => $this->start, 'end' => $this->end]
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

    function updateRecommendTables(bool $betweenUpdateTime = true)
    {
        $this->start = $betweenUpdateTime ? OpenChatServicesUtility::getModifiedCronTime('now')->format('Y-m-d H:i:s') : '2023/10/16 00:00:00';
        $this->end = $betweenUpdateTime ? OpenChatServicesUtility::getModifiedCronTime(strtotime('+1hour'))->format('Y-m-d H:i:s') : '2100/10/16 00:00:00';

        $delete = fn (string $table) => DB::execute(
            "DELETE FROM {$table} WHERE id IN (SELECT id FROM open_chat WHERE updated_at BETWEEN :start AND :end)",
            ['start' => $this->start, 'end' => $this->end]
        );

        clearstatcache();

        $delete('recommend');
        $this->updateName();
        $this->updateDescription('oc.name', 'recommend');
        $this->updateDescription();

        $delete('oc_tag');
        $this->updateDescription('oc.name', 'oc_tag');
        $this->updateDescription(table: 'oc_tag');
        $this->updateName(table: 'oc_tag');

        $delete('oc_tag2');
        $this->updateDescription2('oc.name');
        $this->updateDescription2();
        $this->updateName2();
        $this->updateName2('oc.description');
    }
}
