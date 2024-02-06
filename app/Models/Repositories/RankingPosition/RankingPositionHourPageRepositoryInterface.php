<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourPageRepoDto;

interface RankingPositionHourPageRepositoryInterface
{
    public function getHourRankingPositionTimeAsc(int $open_chat_id, int $category, int $intervalHour): RankingPositionHourPageRepoDto|false;

    public function getHourRisingPositionTimeAsc(int $open_chat_id, int $category, int $intervalHour): RankingPositionHourPageRepoDto|false;
}
