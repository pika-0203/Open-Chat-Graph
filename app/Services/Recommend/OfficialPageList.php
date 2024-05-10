<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Models\RecommendRepositories\OfficialRoomRankingRepository;
use App\Services\Recommend\Dto\RecommendListDto;
use App\Services\Recommend\Enum\RecommendListType;

class OfficialPageList
{
    function __construct(
        private OfficialRoomRankingRepository $recommendPageRepository,
        private RecommendRankingBuilder $recommendRankingBuilder,
    ) {
    }

    /** @return array{ 0:RecommendListDto,1:array{ hour:?int,hour24:?int,week:?int } }|false */
    function getListDto(string $emblem = '', string $listName = 'スペシャル・公式認証オープンチャット'): array|false
    {
        $dto = $this->recommendRankingBuilder->getRanking(
            RecommendListType::Official,
            $emblem,
            $listName,
            $this->recommendPageRepository
        );

        //return $dto ? [$dto, $this->recommendPageRepository->getTagDiffMember($tag)] : false;
        return $dto ? [$dto, []] : false;
    }

    /** @return string[] */
    function getFilterdTags(array $recommendList): array
    {
        $tags = sortAndUniqueArray(
            array_merge(
                array_column($recommendList, 'tag1'),
                array_column($recommendList, 'tag2'),
            ),
            1
        );

        return $tags;
    }
}
