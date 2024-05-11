<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Models\RecommendRepositories\CategoryRankingRepository;
use App\Models\RecommendRepositories\RecommendRankingRepository;
use App\Services\Recommend\Dto\RecommendListDto;
use App\Services\Recommend\Enum\RecommendListType;

class RecommendGenarator
{
    function __construct(
        private RecommendRankingRepository $recommendRankingRepository,
        private CategoryRankingRepository $categoryRankingRepository,
        private RecommendRankingBuilder $recommendRankingBuilder,
    ) {
    }

    function getRecomendRanking(string $tag): RecommendListDto|false
    {
        return $this->recommendRankingBuilder->getRanking(
            RecommendListType::Tag,
            $tag,
            $tag,
            $this->recommendRankingRepository
        );
    }

    function getCategoryRanking(int $open_chat_id): RecommendListDto|false
    {
        $category = $this->categoryRankingRepository->getCategory($open_chat_id);
        if (!$category) return false;

        $listName = getCategoryName($category);

        return $this->recommendRankingBuilder->getRanking(
            RecommendListType::Category,
            (string)$category,
            $listName,
            $this->categoryRankingRepository
        );
    }

    /** @return array{0:RecommendListDto|false,1:RecommendListDto|false,2:string|false,1:RecommendListDto|false} */
    function getRecommend(int $open_chat_id): array
    {
        $tag = $this->recommendRankingRepository->getRecommendTag($open_chat_id);
        if (!$tag) {
            return [
                false,
                false,
                '',
                $this->getCategoryRanking($open_chat_id)
            ];
        }

        [$tag2, $tag3] = $this->recommendRankingRepository->getTags($open_chat_id);
        if ($tag === $tag2) $tag2 = $tag3;

        return [
            $this->getRecomendRanking($tag),
            $tag2 ? $this->getRecomendRanking($tag2) : false,
            $tag,
            $this->getCategoryRanking($open_chat_id),
        ];
    }
}
