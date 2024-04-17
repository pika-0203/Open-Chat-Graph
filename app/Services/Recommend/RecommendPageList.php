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
        'SNS',
        'Instagram（インスタ）',
        '著作権（知的財産権）',
        "東京",
        "北海道",
        "神奈川",
        "愛知",
        "京都",
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
        'オリキャラ恋愛' => ['オリキャラ', 'オリキャラ BL', 'なりきり'],
        '恋愛相談' => ['恋愛', '垢抜け', '失恋', 'メンタルヘルス'],
        '失恋' => ['メンタルヘルス'],
        '不登校' => ['メンタルヘルス', 'ネッ友', 'うつ病', '発達障害', 'HSP'],
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
