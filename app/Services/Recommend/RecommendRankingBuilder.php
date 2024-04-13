<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Config\AppConfig;
use App\Models\RecommendRepositories\RecommendRankingRepositoryInterface;
use App\Services\Recommend\Dto\RecommendListDto;
use App\Services\Recommend\Enum\RecommendListType;

class RecommendRankingBuilder
{
    function getRanking(
        RecommendListType $type,
        int $id,
        string $entity,
        string $listName,
        RecommendRankingRepositoryInterface $repository
    ): RecommendListDto|false {
        $limit = AppConfig::RECOMMEND_LIST_LIMIT;

        $ranking = $repository->getRanking(
            $id,
            $entity,
            AppConfig::RankingHourTable,
            AppConfig::MIN_MEMBER_DIFF_HOUR,
            $limit
        );

        $idArray = array_column($ranking, 'id');
        $ranking2 = $repository->getRankingByExceptId(
            $id,
            $entity,
            AppConfig::RankingDayTable,
            AppConfig::MIN_MEMBER_DIFF_H24,
            $idArray,
            $limit
        );

        $count = count($ranking) + count($ranking2);
        $idArray = array_column(array_merge($ranking, $ranking2), 'id');
        $ranking3 = $repository->getRankingByExceptId(
            $id,
            $entity,
            AppConfig::RankingWeekTable,
            AppConfig::MIN_MEMBER_DIFF_WEEK,
            $idArray,
            $limit
        );

        $count = count($ranking) + count($ranking2) + count($ranking3);
        $idArray = array_column(array_merge($ranking, $ranking2, $ranking3), 'id');
        $ranking4 = $repository->getListOrderByMemberDesc(
            $id,
            $entity,
            $idArray,
            $count < AppConfig::RECOMMEND_LIST_LIMIT ? min(AppConfig::RECOMMEND_LIST_LIMIT - $count, 10) : 3
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
