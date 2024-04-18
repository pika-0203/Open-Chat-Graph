<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Models\RecommendRepositories\RecommendPageRepository;
use App\Services\Recommend\Dto\RecommendListDto;
use App\Services\Recommend\Enum\RecommendListType;

class RecommendPageList
{
    const TagFilter = [
        'スマホ',
        '営業',
        '対荒らし',
        '大人',
        'スタンプ',
        'Instagram（インスタ）',
        '著作権（知的財産権）',
        "東京",
        "北海道",
        "神奈川",
        "愛知",
        "京都",
        "下ネタ",
        "大阪",
        "兵庫",
        "福岡",
        "関東",
        "関西",
        "九州",
        "沖縄",
        "即承認",
        "海外",
        "全国 雑談",
        "70代",
        "60代",
        "50代",
        "加工",
        "フェス",
        "自衛隊",
        "レスバ",
        "unistyle",
        "jobhunt",
        "邦画",
    ];

    const FilteredTagSort = [
        'ガンダム' => ['ガンプラ'],
        'ガンプラ' => ['ガンダム'],
        'アニメ' => ['アニソン'],
        'にじさんじ' => ['にじさんじなりきり'],
        'ボイメで歌（歌リレー）' => ['ライブトーク', '歌ってみた', '歌い手のトークルーム', 'VOCALOID（ボーカロイド／ボカロ）', 'イケボ', 'カラオケ', 'ボイストレーニング（ボイトレ）', 'アニソン'],
        '生成AI・ChatGPT' => ['画像生成AI・AIイラスト'],
        'メンタルヘルス' => ['雑談', '愚痴', '不登校', '発達障害', '精神疾患', 'HSP', '恋愛相談'],
        'HSP' => ['雑談', '愚痴', '不登校', '発達障害', '精神疾患', 'メンタルヘルス', '恋愛相談'],
        'カウンセリング' => ['雑談', '愚痴', '不登校', '発達障害', '精神疾患', 'HSP', 'メンタルヘルス', '恋愛相談'],
        '発達障害' => ['雑談', '愚痴', '不登校', '精神疾患', 'HSP', 'メンタルヘルス', '恋愛相談'],
        'オリキャラ恋愛' => ['オリキャラ', 'オリキャラ BL', 'なりきり'],
        '恋愛相談' => ['恋愛', '垢抜け', '失恋', 'メンタルヘルス', '雑談', '学生限定', '愚痴', '恋バナ'],
        '恋バナ' => ['恋愛', '垢抜け', '失恋', '恋愛相談', 'メンタルヘルス', '雑談', '学生限定', '愚痴'],
        '恋愛' => ['恋愛', 'メンタルヘルス', '雑談', '学生限定', '愚痴', '恋バナ'],
        'ライブトーク' => ['恋愛', '恋バナ', '失恋', 'メンタルヘルス', '雑談', '学生限定', '愚痴', 'ボイメで歌（歌リレー）'],
        '失恋' => ['メンタルヘルス', '雑談', '学生限定', '恋愛', '恋愛相談', '恋話', '愚痴'],
        '学生限定' => ['小学生・中学生限定', '中学生・高校生限定', '中学生限定', '高校生限定', '中学生', '高校生', '雑談', 'ボイメで歌（歌リレー）', '恋バナ'],
        '小学生・中学生限定' => ['学生限定', '中学生・高校生限定', '中学生限定', '中学生', '雑談', 'ボイメで歌（歌リレー）', '恋バナ'],
        '中学生・高校生限定' => ['小学生・中学生限定', '学生限定', '中学生限定', '高校生限定', '中学生', '高校生', '雑談', 'ボイメで歌（歌リレー）', '恋バナ'],
        '中学生限定' => ['小学生・中学生限定', '学生限定', '中学生・高校生限定', '中学生', '雑談', 'ボイメで歌（歌リレー）', '恋バナ'],
        '高校生限定' => ['中学生・高校生限定', '学生限定', '高校生', '雑談', 'ボイメで歌（歌リレー）', '恋バナ'],
        '中学生' => ['小学生・中学生限定', '中学生限定', '中学生・高校生限定', '女子限定', '雑談', '恋バナ'],
        '高校生' => ['中学生・高校生限定', '学生限定', '高校生限定', '雑談', 'ボイメで歌（歌リレー）', '恋バナ'],
        '雑談' => ['学生限定', 'ボイメで歌（歌リレー）', '愚痴', '恋バナ', 'メンタルヘルス', '恋愛相談', '大喜利'],
        'ボイメで歌（歌リレー）' => ['学生限定', '恋愛', '雑談'],
        '不登校' => ['メンタルヘルス', 'ネッ友', 'うつ病', '発達障害', 'HSP'],
        'メルカリ' => ['せどり', '副業', 'ポイ活', 'お金', 'TEMU', 'SHEIN', 'クーポン・無料配布', 'スタバ', 'KAUCHE（カウシェ）', '仮想通貨', 'FX', '億り人'],
        'KAUCHE（カウシェ）' => ['せどり', '副業', 'ポイ活', 'お金', 'TEMU', 'SHEIN', 'クーポン・無料配布', 'スタバ', 'メルカリ', '仮想通貨', 'FX', '億り人'],
        'TEMU' => ['せどり', '副業', 'ポイ活', 'お金', 'KAUCHE（カウシェ）', 'SHEIN', 'クーポン・無料配布', 'スタバ', 'メルカリ', '仮想通貨', 'FX', '億り人'],
        'SHEIN' => ['せどり', '副業', 'ポイ活', 'お金', 'KAUCHE（カウシェ）', 'TEMU', 'クーポン・無料配布', 'スタバ', 'メルカリ', '仮想通貨', 'FX', '億り人'],
        'せどり' => ['副業', 'お金', 'ポイ活', 'クーポン・無料配布', 'メルカリ', '仮想通貨', 'NISA', '億り人', 'ふるさと納税', 'TEMU', 'SHEIN', 'KAUCHE（カウシェ）'],
        '副業' => ['せどり', 'お金', 'ポイ活', 'クーポン・無料配布', 'メルカリ', '仮想通貨', '投資', 'NISA', '億り人', 'FX', 'KAUCHE（カウシェ）'],
        'お金' => ['せどり', '副業', 'ポイ活', 'クーポン・無料配布', 'TEMU', 'SHEIN', 'メルカリ', '仮想通貨', '投資', 'NISA', '億り人', 'KAUCHE（カウシェ）', 'FX'],
        '節約' => ['ふるさと納税', '節税', 'TEMU', 'SHEIN', 'メルカリ', 'KAUCHE（カウシェ）', 'ポイ活', 'クーポン・無料配布', 'NISA', '億り人', 'お金'],
        'クーポン・無料配布' => ['TEMU', 'SHEIN', 'メルカリ', 'KAUCHE（カウシェ）', 'ポイ活', 'ふるさと納税', 'NISA', '億り人', 'スタバ', 'お金'],
        'ポイ活' => ['せどり', '副業', 'ふるさと納税', 'クーポン・無料配布', 'TEMU', 'SHEIN', 'メルカリ', 'NISA', '億り人', 'KAUCHE（カウシェ）', 'お金', '投資'],
        '億り人' => ['お金', '仮想通貨', 'Coin', '投資', 'NISA', '株式投資', 'FX', '副業', 'せどり'],
        '投資' => ['お金', '仮想通貨', 'Coin', 'せどり', 'NISA', '株式投資', 'FX', '副業', '億り人'],
        '株式投資' => ['お金', '仮想通貨', 'Coin', 'せどり', 'NISA', '投資', 'FX', '副業', '億り人'],
        '仮想通貨' => ['お金', '株式投資', 'Coin', 'せどり', 'NISA', '投資', 'FX', '副業', '億り人'],
        'FX' => ['お金', '仮想通貨', 'Coin', '投資', 'NISA', '株式投資', '億り人', '副業', 'せどり'],
        'Coin' => ['お金', '仮想通貨', 'FX', '投資', 'NISA', '株式投資', '億り人', '副業', 'せどり'],
        'NISA' => ['お金', '仮想通貨', 'FX', '投資', 'Coin', '株式投資', '億り人', '副業', 'せどり'],
        '競艇予想' => ['お金', '仮想通貨', '投資', 'FX', '株式投資', '億り人', 'Coin', '副業', '競馬予想', 'パチンコ・スロット（パチスロ）'],
        '競馬予想' => ['お金', '仮想通貨', '投資', 'FX', '株式投資', '億り人', 'Coin', '副業', '競艇予想', 'パチンコ・スロット（パチスロ）'],
        'パチンコ・スロット（パチスロ）' => ['お金', '仮想通貨', 'Coin', '投資', 'FX', '株式投資', '億り人', '副業', '競艇予想', '競馬予想'],
        'ITエンジニア' => ['プログラミング', 'Webエンジニア・プログラミング', 'マーケティング', 'WEBデザイナー・デザイン', 'フリーランス', 'デザイナー', '生成AI・ChatGPT', 'SNS', 'YouTuber'],
        'プログラミング' => ['ITエンジニア', 'Webエンジニア・プログラミング', 'マーケティング', 'WEBデザイナー・デザイン', 'フリーランス', 'デザイナー', '生成AI・ChatGPT', 'SNS', 'YouTuber'],
        'Webエンジニア・プログラミング' => ['ITエンジニア', 'プログラミング', 'マーケティング', 'WEBデザイナー・デザイン', 'フリーランス', 'デザイナー', '生成AI・ChatGPT', 'SNS', 'YouTuber'],
        'WEBデザイナー・デザイン' => ['ITエンジニア', 'プログラミング', 'マーケティング', 'Webエンジニア・プログラミング', 'フリーランス', 'デザイナー', '生成AI・ChatGPT', 'SNS', 'YouTuber'],
        'フリーランス' => ['ITエンジニア', 'プログラミング', 'マーケティング', 'Webエンジニア・プログラミング', 'WEBデザイナー・デザイン', 'デザイナー', '生成AI・ChatGPT', 'SNS', 'YouTuber'],
        'マーケティング' => ['ITエンジニア', 'プログラミング', 'フリーランス', 'Webエンジニア・プログラミング', 'WEBデザイナー・デザイン', 'デザイナー', '生成AI・ChatGPT', 'SNS', 'YouTuber'],
        'SNS' => ['ITエンジニア', 'プログラミング', 'フリーランス', 'Webエンジニア・プログラミング', 'WEBデザイナー・デザイン', 'デザイナー', '生成AI・ChatGPT', 'マーケティング', 'YouTuber'],
    ];

    function __construct(
        private RecommendPageRepository $recommendPageRepository,
        private RecommendRankingBuilder $recommendRankingBuilder,
    ) {
    }

    /** @return array{ 0:RecommendListDto,1:array{ hour:?int,hour24:?int,week:?int } }|false */
    function getListDto(string $tag): array|false
    {
        $dto = $this->recommendRankingBuilder->getRanking(
            RecommendListType::Tag,
            0,
            $tag,
            $tag,
            $this->recommendPageRepository
        );

        //return $dto ? [$dto, $this->recommendPageRepository->getTagDiffMember($tag)] : false;
        return $dto ? [$dto, []] : false;
    }


    function isValidTag(string $tag): bool
    {
        /** @var RecommendUpdater $recommendUpdater */
        $recommendUpdater = app(RecommendUpdater::class);
        $tags = $recommendUpdater->getAllTagNames();
        return in_array($tag, $tags);
    }

    /** @return string[] */
    function getFilterdTags(array $recommendList, string $tag): array
    {
        $tagStr = RecommendUtility::extractTag($tag);

        $tags = sortAndUniqueArray(
            array_merge(
                array_column($recommendList, 'tag1'),
                array_column($recommendList, 'tag2'),
                self::FilteredTagSort[$tag] ?? []
            ),
            1
        );

        $tags = array_filter($tags, fn ($e) => !in_array($e, self::TagFilter) && $e !== $tag);

        $tagsStr = array_map(fn ($t) => RecommendUtility::extractTag($t), $tags);
        uksort($tags, function ($a) use ($tagStr, $tagsStr, $tag, $tags) {
            return str_contains($tagsStr[$a], $tagStr) || (isset(self::FilteredTagSort[$tag]) && in_array($tags[$a], self::FilteredTagSort[$tag])) ? -1 : 1;
        });

        return $tags;
    }
}
