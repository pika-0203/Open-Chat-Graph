<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourPageRepoDto;
use App\Services\OpenChat\Enum\RankingType;

interface RankingPositionHourPageRepositoryInterface
{
    public function getHourPosition(
        RankingType $type,
        int $open_chat_id,
        int $category,
        int $intervalHour,
        \DateTime $endTime
    ): RankingPositionHourPageRepoDto;

    /** @return array{ time:string,position:int,total_count_ranking:int } */
    public function getFinalRankingPosition(int $open_chat_id, int $category): array|false;
}
