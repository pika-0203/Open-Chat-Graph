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
}
