<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Services\Recommend\Dto\RecommendListDto;
use App\Services\Recommend\StaticData\RecommendStaticDataFile;

class RecommendGenarator
{
    function __construct(
        private RecommendStaticDataFile $recommendStaticDataFile
    ) {
    }

    /** @return array{0:RecommendListDto|false,1:RecommendListDto|false,2:string,3:RecommendListDto|false} */
    function getRecommend(?string $tag, ?string $tag2, ?string $tag3, ?int $category): array
    {
        if (!$tag) {
            return [
                false,
                false,
                '',
                $category ? $this->recommendStaticDataFile->getCategoryRanking($category) : false
            ];
        }

        if ($tag === $tag2) $tag2 = $tag3;
        return [
            $this->recommendStaticDataFile->getRecomendRanking($tag),
            $tag2 ? $this->recommendStaticDataFile->getRecomendRanking($tag2) : false,
            $tag,
            $category ? $this->recommendStaticDataFile->getCategoryRanking($category) : false
        ];
    }
}
