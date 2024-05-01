<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\Dto\RankingPositionPageRepoDto;
use App\Services\OpenChat\Enum\RankingType;

interface RankingPositionPageRepositoryInterface
{
    public function getDailyPosition(
        RankingType $type,
        int $open_chat_id,
        int $category
    ): RankingPositionPageRepoDto;

    /** @return array{ time:string,position:int,total_count_ranking:int } */
    public function getFinalRankingPosition(int $open_chat_id, int $category): array|false;
}
