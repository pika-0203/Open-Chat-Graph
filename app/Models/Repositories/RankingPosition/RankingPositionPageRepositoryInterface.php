<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\Dto\RankingPositionPageRepoDto;

interface RankingPositionPageRepositoryInterface
{
    public function getDailyRankingPositionTimeAsc(int $open_chat_id, int $category): RankingPositionPageRepoDto|false;

    public function getDailyRisingPositionTimeAsc(int $open_chat_id, int $category): RankingPositionPageRepoDto|false;

    public function getFirstTime(int $open_chat_id): \DateTime|false;
}
