<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Services\Cron\CronJson\SyncOpenChatState;
use App\Services\OpenChat\OpenChatApiDbMerger;
use App\Services\DailyUpdateCronService;
use App\Services\RankingPosition\Persistence\RankingPositionHourPersistenceLastHourChecker;
use App\Services\RankingPosition\RankingPositionHourUpdater;
use App\Services\SitemapGenerator;

class SyncOpenChat
{
    function __construct(
        private SyncOpenChatState $state,
        private OpenChatApiDbMerger $merger,
        private SitemapGenerator $sitemap,
        private RankingPositionHourUpdater $rankingPositionHour,
        private RankingPositionHourPersistenceLastHourChecker $rankingPositionHourChecker,
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
        if (isDailyUpdateTime()) {
            set_time_limit(3600 * 2);
            $this->hourlyTask();
            $this->dailyTask();
        } else {
            set_time_limit(3000);
            $this->hourlyTask();
        }

        $this->finalize();
    }

    private function hourlyTask()
    {
        $this->hourlyRankingPositionCheckLastHour();
        $this->hourlyMerge();
        $this->hourlyRankingPosition();

        $this->state->isActive = false;
        $this->state->update();
    }

    private function hourlyRankingPositionCheckLastHour()
    {
        $this->rankingPositionHourChecker->checkLastHour();
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

    private function dailyTask()
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
