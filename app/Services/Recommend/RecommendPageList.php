<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Services\Recommend\Dto\RecommendListDto;
use App\Services\Recommend\StaticData\RecommendStaticDataFile;

class RecommendPageList
{
    function __construct(
        private RecommendStaticDataFile $recommendStaticDataGenerator,
        private RecommendUpdater $recommendUpdater,
    ) {
    }

    /** @return array{ 0:RecommendListDto,1:array{ hour:?int,hour24:?int,week:?int } }|false */
    function getListDto(string $tag): array|false
    {
        $dto = $this->recommendStaticDataGenerator->getRecomendRanking($tag);

        //return $dto ? [$dto, $this->recommendPageRepository->getTagDiffMember($tag)] : false;
        return $dto ? [$dto, []] : false;
    }

    function isValidTag(string $tag): bool
    {
        $tags = $this->recommendUpdater->getAllTagNames();
        return in_array($tag, $tags);
    }
}
