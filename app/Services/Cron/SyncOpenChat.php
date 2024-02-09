<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Services\Cron\CronJson\SyncOpenChatState;
use App\Services\OpenChat\OpenChatApiDbMerger;
use App\Models\Repositories\Log\LogRepositoryInterface;
use App\Services\DailyUpdateCronService;
use App\Services\RankingPosition\RankingPositionHourUpdater;
use App\Services\SitemapGenerator;

class SyncOpenChat
{
    function __construct(
        private SyncOpenChatState $state,
        private OpenChatApiDbMerger $merger,
        private LogRepositoryInterface $log,
        private SitemapGenerator $sitemap,
        private RankingPositionHourUpdater $rankingPositionHour,
    ) {
        $this->state->isActive = true;
        $this->state->update();
    }

    function __destruct()
    {
        $this->state->isActive = false;
        $this->state->update();
    }

    function handle()
    {
        if (true || isDailyUpdateTime()) {
            set_time_limit(3600 * 2);
            $this->hourlyMerge();
            $this->hourlyRankingPosition();
            
            $this->state->isActive = false;
            $this->state->update();

            $this->dailyUpdate();
        } else {
            set_time_limit(1800);
            $this->hourlyMerge();
            $this->hourlyRankingPosition();
        }

        $this->finalize();
    }

    private function hourlyMerge()
    {
        OpenChatApiDbMerger::disableKillFlag();
        addCronLog("start merge");

        $result = $this->merger->fetchOpenChatApiRankingAll();
        addCronLog(
            array_map(
                fn ($r) => $r['category'] . ' count:' . $r['count'] . ' time:' . $r['dateTime']->format('H:i:s'),
                $result
            )
        );
    }

    private function hourlyRankingPosition()
    {
        $this->rankingPositionHour->crawlRisingAndUpdateRankingPositionHourDb();
    }

    private function dailyUpdate()
    {
        /** @var DailyUpdateCronService $updater */
        $updater = app(DailyUpdateCronService::class);
        $updater->update();
    }

    function finalize(): void
    {
        $this->sitemap->generate();
    }
}
