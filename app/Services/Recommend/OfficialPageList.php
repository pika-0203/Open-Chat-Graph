<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Services\Recommend\Dto\RecommendListDto;
use App\Services\Recommend\StaticData\RecommendStaticDataFile;

class OfficialPageList
{
    function __construct(
        private RecommendStaticDataFile $recommendStaticDataGenerator,
    ) {
    }

    /** @return array{ 0:RecommendListDto,1:array{ hour:?int,hour24:?int,week:?int } }|false */
    function getListDto(int $emblem): array|false
    {
        $dto = $this->recommendStaticDataGenerator->getOfficialRanking($emblem);

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
