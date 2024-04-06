<?php

declare(strict_types=1);

namespace App\Models\RecommendRepositories;

interface RecommendRankingRepositoryInterface
{
    const Select = "
        oc.id,
        oc.name,
        oc.local_img_url AS img_url,
        oc.member,
        oc.description,
        oc.emblem
    ";

    const SelectPage = "
        oc.id,
        oc.name,
        oc.local_img_url AS img_url,
        oc.member,
        oc.description,
        oc.emblem,
        ranking.tag1,
        ranking.tag2
    ";

    function getRanking(
        int $id,
        string $entity,
        string $table,
        int $minDiffMember,
        int $limit,
    ): array;

    function getRankingByExceptId(
        int $id,
        string $entity,
        string $table,
        int $minDiffMember,
        array $idArray,
        int $limit,
    ): array;

    function getListOrderByMemberDesc(
        int $id,
        string $entity,
        array $idArray,
        int $limit,
    ): array;
}
