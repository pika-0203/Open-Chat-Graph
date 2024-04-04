<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Config\AppConfig;
use App\Models\RecommendRepositories\CategoryRankingRepository;
use App\Models\RecommendRepositories\RecommendRankingRepository;
use App\Models\RecommendRepositories\RecommendRankingRepositoryInterface;
use App\Services\Recommend\Dto\RecommendListDto;
use App\Services\Recommend\Enum\RecommendListType;

class RecommendGenarator
{
    private const LIST_LIMIT = 50;
    private const MIN_MEMBER_DIFF = 3;

    function __construct(
        private RecommendRankingRepository $recommendRankingRepository,
        private CategoryRankingRepository $categoryRankingRepository,
    ) {
    }

    function getRanking(RecommendListType $type, int $id, string $entity, string $listName): RecommendListDto
    {
        $limit = self::LIST_LIMIT;
        $minDiffMember = self::MIN_MEMBER_DIFF;

        /** @var RecommendRankingRepositoryInterface $repository */
        $repository = match ($type) {
            $type::Category => $this->categoryRankingRepository,
            $type::Tag => $this->recommendRankingRepository
        };

        $ranking = $repository->getRanking($id, $entity, AppConfig::RankingHourTable, $minDiffMember, $limit);

        $idArray = array_column($ranking, 'id');
        $ranking2 = $repository->getRankingByExceptId($id, $entity, AppConfig::RankingDayTable, $minDiffMember, $idArray, $limit);

        $idArray = array_column(array_merge($ranking, $ranking2), 'id');
        $ranking3 = $repository->getRankingByExceptId($id, $entity, AppConfig::RankingWeekTable, $minDiffMember, $idArray, $limit);

        $idArray = array_column(array_merge($ranking, $ranking2, $ranking3), 'id');
        $ranking4 = $repository->getListOrderByMemberDesc($id, $entity, $idArray, $limit);

        return new RecommendListDto($type, $listName, $ranking, $ranking2, $ranking3, $ranking4);
    }

    function formatTag(string $tag): string
    {
        $listName = mb_strstr($tag, '_OR_', true) ?: $tag;
        $listName = str_replace('_AND_', ' ', $listName);
        return $listName;
    }

    function getRecomendRanking(int $open_chat_id, string $tag): RecommendListDto
    {
        $listName = $this->formatTag($tag);
        return $this->getRanking(RecommendListType::Tag, $open_chat_id, $tag, $listName);
    }

    function getCategoryRanking(int $open_chat_id): RecommendListDto|false
    {
        $category = $this->categoryRankingRepository->getCategory($open_chat_id);
        if (!$category) return false;

        $listName = array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category];
        return $this->getRanking(RecommendListType::Category, $open_chat_id, (string)$category, $listName);
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

        return [$result1, $result2, $recommendTag ? $this->formatTag($recommendTag) : ''];
    }
}
