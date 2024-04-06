<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Config\AppConfig;
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

    function getRecomendRanking(int $open_chat_id, string $tag): RecommendListDto|false
    {
        return $this->recommendRankingBuilder->getRanking(
            RecommendListType::Tag,
            $open_chat_id,
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
            $open_chat_id,
            (string)$category,
            $listName,
            $this->categoryRankingRepository
        );
    }

    /** @return array{0:RecommendListDto|false,1:RecommendListDto|false,2:string|false} */
    function getRecommend(int $open_chat_id): array
    {
        [$tag, $tag2] = $this->recommendRankingRepository->getTags($open_chat_id);
        $recommendTag = $this->recommendRankingRepository->getRecommendTag($open_chat_id);

        if (!$tag) $tag = $tag2;
        if ($tag === $tag2) $tag2 = false;

        if (!$tag) $tag = $recommendTag;

        if (!$tag) {
            $result = $this->getCategoryRanking($open_chat_id);
            return [$result, false, ''];
        }

        $result1 = $this->getRecomendRanking($open_chat_id, $tag);
        $result2 = $tag2
            ? $this->getRecomendRanking($open_chat_id, $tag2)
            : $this->getCategoryRanking($open_chat_id);

        return [
            $result1,
            $result2,
            $recommendTag ?: ''
        ];
    }
}
