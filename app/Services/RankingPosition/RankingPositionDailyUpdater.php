<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepository;
use App\Services\RankingPosition\Persistence\RankingPositionDailyPersistence;
use Shadow\DB;

class RankingPositionDailyUpdater
{
    function __construct(
        private RankingPositionDailyPersistence $rankingPositionDailyPersistence
    ) {
    }

    function updateYesterdayRankingPositionDailyDb()
    {
        DB::$pdo = null;
        OpenChatDataForUpdaterWithCacheRepository::clearCache();

        $this->rankingPositionDailyPersistence->persistHourToDaily();
    }
}
