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
        '生成AI・ChatGPT' => ['画像生成AI・AIイラスト']
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
        $tags = sortAndUniqueArray(
            array_merge(
                array_column($recommendList, 'tag1'),
                array_column($recommendList, 'tag2')
            ),
            1
        );

        $tags = array_filter($tags, fn ($e) => !in_array($e, self::TagFilter) && $e !== $tag);

        $tags2 = array_map(fn ($t) => RecommendUtility::extractTag($t), $tags);
        $tag2 = RecommendUtility::extractTag($tag);
        uksort($tags, function ($a) use ($tag2, $tags2, $tag) {
            return str_contains($tags2[$a], $tag2) || (isset(self::FilteredTagSort[$tag]) && in_array($tags2[$a], self::FilteredTagSort[$tag])) ? -1 : 1;
        });

        return $tags;
    }
}
