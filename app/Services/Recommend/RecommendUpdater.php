<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Config\AppConfig;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use Shadow\DB;

class RecommendUpdater
{
    const NAME_STRONG_TAG = [
        "下ネタ_OR_下あり_OR_下○タ_OR_下系",
        "ネッ友_OR_ネ友",
        "MBTI_OR_ISTJ_OR_ISFJ_OR_INFJ_OR_INTJ_OR_ISTP_OR_ISFP_OR_INFP_OR_INTP_OR_ESTP_OR_ESFP_OR_ENFP_OR_ENTP_OR_ESTJ_OR_ESFJ_OR_ENFJ_OR_ENTJ",
        ["LGBT", ["ゲイ_AND_バイ", "同性愛_OR_LGBT_OR_ゲイ学生_OR_Xジェンダー_OR_トランスジェンダー_OR_セクマイ_OR_ノンセク_OR_レズビアン"]],
        "IT資格_OR_基本情報技術者_OR_応用情報技術者_OR_ITパスポート_OR_情報処理試験_OR_ITストラテジ",
        "新入生同士の情報交換_OR_年度入学_OR_度新入生_OR_新入生同士",
        ["大学 新入生", ["春から_AND_大学", "新入生_AND_大学"]],
        "就活情報_OR_就活生向け情報_OR_就活生情報_OR_選考対策・企業研究_OR_就活選考対策_OR_就活対策グループ_OR_就活テスト対策グループ_OR_志望者向けグループ_OR_業界志望者向け_OR_就活の情報_OR_就活会議_OR_就活生向け_OR_就活対策用_OR_就活生の情報交換_OR_unistyle_OR_就活の情報共有",
        "AI画像生成_OR_AIイラスト_OR_ばりぐっどくん_OR_AI絵画_OR_AI絵師",
        "ポイ活",
        "クーポン_OR_お得情報",
        "電車_OR_鉄道_OR_撮り鉄_OR_プラレール_OR_列車_OR_乗り鉄_OR_近鉄_OR_スジ鉄",
        ["全国 雑談", ["全国_AND_オプチャ", "全国_AND_雑談",]],
        "ケアマネージャー_OR_ケアマネ",
        "BLTトレードシステムサポートオプチャ",
        "Crypto_AND_Academy",
        ["オリキャラ恋愛", ["オリキャラ恋愛_OR_折恋_OR_折 恋", "オリキャラ_AND_恋愛"]],
        ["地雷系", ["地雷系_OR_量産型", "地雷_AND_量産"]],
        "偽カップル_OR_偽カプ",
        "ぷせゆる",
        "バウンティラッシュ_OR_バウンティ",
        "スピリチュアル",
        "ナイチンゲールダンス",
        "占い師",
        "占い_OR_霊視",
        ["ボイメ 歌", ["ボイメ_AND_歌", "歌リレー", "歌王国"]],
        "ヒカマニ",
        "LAST WAR_OR_lastwar",
        "日常組",
        "防災_OR_災害",
        "マジックカード",
        "イケボ",
        "独り言",
        "ミニ四駆",
        "イナズマイレブン_OR_イナイレ",
        "吃音",
        "ぽっちゃり",
        "カラオケ",
        "セミナー",
        "モンハン",
        "SNS",
        "インスタ",
        "ぷにぷに",
        "メンヘラ",
        "すとぷり",
        "いれいす",
        "AMPTAK",
        "ZEROBASEONE_OR_ゼベワン_OR_ゼロベースワン_OR_ZB1",
        "コレコレ_OR_コレリス",
        "推しの子",
        "カラフルピーチ_OR_からぴち_OR_カラピチ_OR_からピチ",
        "ちろぴの",
        "マッシュル",
        "文豪ストレイドッグス_OR_文スト",
        "ヒプノシスマイク_OR_ヒプマイ",
        "ホロライブ",
        "カーパーキング",
        "にじさんじ",
        "東方",
        "対荒らし_OR_対荒_OR_白夜総会_OR_ヤブ医者_OR_拓也集落_OR_植民地",
        "レスバ_OR_喧嘩",
        "女性限定",
        "男性限定",
        "男子限定",
        "女子限定",
        ["小学生・中学生・高校生限定", ["小学生・中学生・高校生限定_OR_小中高生限定_OR_小学生、中学生、高校生限定_OR_小・中・高生限定_OR_小・中・高校生限定", "小学生〜高校生_AND_限定"]],
        "中学生・高校生限定_OR_中高生限定_OR_中学生、高校生限定_OR_中・高生限定",
        "小学生・中学生限定_OR_小中学生限定_OR_小学生、中学生限定_OR_小・中学生限定",
        "中学生限定_OR_中学生だけ",
        "高校生限定",
        "学生限定",
        "ライブトーク",
        "ちいかわ",
        "アオのハコ",
        "ツイステッドワンダーランド_OR_ツイステ",
        "フリーレン",
        "スジ公開",
        "ブルーロック",
        "ラブライブ_OR_ラブライバー",
        "SEKAI NO OWARI_OR_セカオワ",
        "不登校",
        "発達障害_OR_ADHD_OR_自閉症_OR_カサンドラ_OR_軽度知的障害_OR_アスペルガー_OR_双極性障害",
        "うつ病_OR_鬱病",
        "精神疾患_OR_精神障害",
        "知的障害_OR_境界知能",
        "知的財産_OR_著作権_OR_知財_OR_肖像権",
        "なりきり_OR_全也_OR_nrkr_OR_#也_OR_D也_OR_ゆるなり_OR_緩也_OR_夢也_OR_夢 也",
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
        ["競馬 予想", ["競馬", "競馬_AND_予想"]],
        ["競艇 予想", ["競艇", "競艇_AND_予想"]],
        "オリキャラ",
        "復縁",
        "愚痴",
        "毒親",
        "恋愛相談",
        "恋愛",
        "宣伝",
        "太鼓の達人",
        "猫ミーム",
        "ブルーアーカイブ_OR_ブルアカ",
    ];

    const DESC_STRONG_TAG = [
        "unistyle",
        "jobhunt",
        "ポイ活",
        ["LGBT", ["ゲイ_AND_バイ", "同性愛_OR_LGBT_OR_ゲイ学生_OR_Xジェンダー_OR_トランスジェンダー_OR_セクマイ_OR_ノンセク_OR_レズビアン"]],
        "Produce 101 Japan_OR_PRODUCE 101_OR_PRODUCE101_OR_日プガールズ",
        ["地雷系", ["地雷系_OR_量産型", "地雷_AND_量産"]],
        "復縁",
        "不登校",
        "毒親",
        "発達障害_OR_ADHD_OR_自閉症_OR_カサンドラ_OR_軽度知的障害_OR_アスペルガー_OR_双極性障害",
        "うつ病_OR_鬱病",
        "知的障害_OR_境界知能",
        "精神疾患_OR_精神障害",
        "障害者",
        "ネッ友_OR_ネ友",
        ["オリキャラ恋愛", ["オリキャラ恋愛_OR_折恋_OR_折 恋", "オリキャラ_AND_恋愛"]],
        "K也_OR_🇰🇷 也_OR_𝐊 也",
        "MBTI_OR_ISTJ_OR_ISFJ_OR_INFJ_OR_INTJ_OR_ISTP_OR_ISFP_OR_INFP_OR_INTP_OR_ESTP_OR_ESFP_OR_ENFP_OR_ENTP_OR_ESTJ_OR_ESFJ_OR_ENFJ_OR_ENTJ",
        "女性限定",
        "男性限定",
        "男子限定",
        "女子限定",
        ["小学生・中学生・高校生限定", ["小学生・中学生・高校生限定_OR_小中高生限定_OR_小学生、中学生、高校生限定_OR_小・中・高生限定_OR_小・中・高校生限定", "小学生〜高校生_AND_限定"]],
        "中学生・高校生限定_OR_中高生限定_OR_中学生、高校生限定_OR_中・高生限定",
        "小学生・中学生限定_OR_小中学生限定_OR_小学生、中学生限定_OR_小・中学生限定",
        "歌い手",
    ];

    const AFTER_DESC_STRONG_TAG = [
        "クーポン_OR_お得情報",
    ];

    /** @var string[] $tags */
    public array $tags;
    private string $start;
    private string $end;

    function replace(string|array $word, string $column): string
    {
        $like = "{$column} COLLATE utf8mb4_general_ci LIKE";

        $rep = function ($str) use ($like) {
            $str = str_replace('_AND_', "%' AND {$like} '%", $str);
            return "{$like} '%" . str_replace('_OR_', "%' OR {$like} '%", $str) . "%'";
        };

        if (is_array($word)) {
            return "(" . implode(") OR (", array_map(fn ($str) => $rep($str), $word[1])) . ")";
        }

        return $rep($word);
    }

    /** @return string[] */
    private function getReplacedTags(string $column): array
    {
        $tags = array_merge(
            self::NAME_STRONG_TAG,
            array_merge(...json_decode(
                file_get_contents(AppConfig::OPEN_CHAT_SUB_CATEGORIES_TAG_FILE_PATH),
                true
            ))
        );

        $this->tags = array_map(fn ($el) => is_array($el) ? $el[0] : $el, $tags);

        return array_map(fn ($str) => $this->replace($str, $column), $tags);
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
            array_map(fn ($str) => $this->replace($str, $column), self::DESC_STRONG_TAG),
            array_map(fn ($str) => $this->replace($str, $column), self::AFTER_DESC_STRONG_TAG)
        ];
    }

    function updateDescription(string $column = 'oc.description', string $table = 'recommend')
    {
        [$tags, $strongTags, $afterStrongTags] = $this->getReplacedTagsDesc($column);

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
                $tag = is_array($tag) ? $tag[0] : $tag;
                $excute($table, $tag, $search, $category);
            }

            foreach ($array as $key => $search) {
                $tag = $this->tags[$category][$key];
                $tag = is_array($tag) ? $tag[0] : $tag;
                $excute($table, $tag, $search, $category);
            }

            foreach ($afterStrongTags as $key => $search) {
                $tag = self::AFTER_DESC_STRONG_TAG[$key];
                $tag = is_array($tag) ? $tag[0] : $tag;
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
        [$tags, $strongTags, $afterStrongTags] = $this->getReplacedTagsDesc($column);

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
                $tag = is_array($tag) ? $tag[0] : $tag;
                $excute($table, $tag, $search, $category);
            }

            foreach ($array as $key => $search) {
                $tag = $this->tags[$category][$key];
                $tag = is_array($tag) ? $tag[0] : $tag;
                $excute($table, $tag, $search, $category);
            }

            foreach ($afterStrongTags as $key => $search) {
                $tag = self::AFTER_DESC_STRONG_TAG[$key];
                $tag = is_array($tag) ? $tag[0] : $tag;
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
