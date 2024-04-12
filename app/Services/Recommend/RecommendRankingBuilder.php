<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Config\AppConfig;
use App\Models\RecommendRepositories\RecommendRankingRepositoryInterface;
use App\Services\Recommend\Dto\RecommendListDto;
use App\Services\Recommend\Enum\RecommendListType;

class RecommendRankingBuilder
{
    private const LIST_LIMIT = 20;
    public const MIN_MEMBER_DIFF = 3;

    function getRanking(
        RecommendListType $type,
        int $id,
        string $entity,
        string $listName,
        RecommendRankingRepositoryInterface $repository
    ): RecommendListDto|false {
        $limit = self::LIST_LIMIT;
        $minDiffMember = self::MIN_MEMBER_DIFF;

        $ranking = $repository->getRanking(
            $id,
            $entity,
            AppConfig::RankingHourTable,
            $minDiffMember + 1,
            $limit
        );

        $idArray = array_column($ranking, 'id');
        $ranking2 = $repository->getRankingByExceptId(
            $id,
            $entity,
            AppConfig::RankingDayTable,
            $minDiffMember,
            $idArray,
            $limit
        );

        $idArray = array_column(array_merge($ranking, $ranking2), 'id');
        $ranking3 = $repository->getRankingByExceptId(
            $id,
            $entity,
            AppConfig::RankingWeekTable,
            $minDiffMember,
            $idArray,
            $limit
        );

        $count = count($ranking) + count($ranking2) + count($ranking3);
        $idArray = array_column(array_merge($ranking, $ranking2, $ranking3), 'id');
        $ranking4 = $repository->getListOrderByMemberDesc(
            $id,
            $entity,
            $idArray,
            $count < self::LIST_LIMIT * 3 ? self::LIST_LIMIT * 3 - $count : 3
        );

        $dto = new RecommendListDto(
            $type,
            $listName,
            $ranking,
            $ranking2,
            $ranking3,
            $ranking4
        );

        return $dto->maxMemberCount ? $dto : false;
    }
}
