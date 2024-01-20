<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourApiDto;

interface RankingPositionHourApiRepositoryInterface
{
    public function getLatestRanking(string $emid, int $category, \DateTime $time): RankingPositionHourApiDto|false;
}
