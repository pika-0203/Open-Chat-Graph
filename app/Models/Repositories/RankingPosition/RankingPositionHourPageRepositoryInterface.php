<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourPageRepoDto;

interface RankingPositionHourPageRepositoryInterface
{
    public function getHourRankingPositionTimeAsc(string $emid, int $category): RankingPositionHourPageRepoDto|false;

    public function getHourRisingPositionTimeAsc(string $emid, int $category): RankingPositionHourPageRepoDto|false;
}
