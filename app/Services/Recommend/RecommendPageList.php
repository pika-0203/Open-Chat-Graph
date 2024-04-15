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
        '大人',
        'スタンプ',
        'SNS',
        'Instagram（インスタ）',
        '知的財産',
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

        return array_filter($tags, fn ($e) => !(in_array($e, self::TagFilter) || $e === $tag));
    }
}
