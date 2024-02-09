<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Models\Repositories\Log\LogRepositoryInterface;
use App\Services\RankingPosition\Crawler\RisingPositionCrawling;
use App\Services\RankingPosition\Persistence\RankingPositionHourPersistence;
use Shadow\DB;

class RankingPositionHourUpdater
{
    function __construct(
        private RisingPositionCrawling $risingPositionCrawling,
        private RankingPositionHourPersistence $rankingPositionHourPersistence,
        private LogRepositoryInterface $logRepository,
    ) {
    }

    /**
     * @throws \RuntimeException
     */
    function crawlRisingAndUpdateRankingPositionHourDb()
    {
        try {
            $this->risingPositionCrawling->risingPositionCrawling();
        } catch (\RuntimeException $e) {
            // 再接続
            DB::$pdo = null;
            $this->logRepository->logUpdateOpenChatError(0, $e->__toString());
            $this->rankingPositionHourPersistence->persistStorageFileToDb();
            throw $e;
        }
        
        $this->rankingPositionHourPersistence->persistStorageFileToDb();
    }
}
