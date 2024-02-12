<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Services\Cron\CronJson\SyncOpenChatState;
use App\Services\OpenChat\OpenChatApiDbMerger;
use App\Services\DailyUpdateCronService;
use App\Services\RankingPosition\Persistence\RankingPositionHourPersistence;
use App\Services\RankingPosition\Persistence\RankingPositionHourPersistenceLastHourChecker;
use App\Services\SitemapGenerator;
use App\Services\UpdateHourlyMemberRankingService;

class SyncOpenChat
{
    function __construct(
        private SyncOpenChatState $state,
        private OpenChatApiDbMerger $merger,
        private SitemapGenerator $sitemap,
        private RankingPositionHourPersistence $rankingPositionHourPersistence,
        private RankingPositionHourPersistenceLastHourChecker $rankingPositionHourChecker,
        private UpdateHourlyMemberRankingService $hourlyMemberRanking,
    ) {
        $this->state->isHourlyTaskActive = true;
        $this->state->update();
    }

    function __destruct()
    {
        $this->state->isHourlyTaskActive = false;
        $this->state->update();
    }

    function handle()
    {
        if (isDailyUpdateTime()) {
            set_time_limit(4500);
            $this->hourlyTask();
            $this->dailyTask();
        } elseif (isDailyUpdateTime(new \DateTime('-2 hour')) && $this->state->isDailyTaskActive) {
            set_time_limit(4500);
            $this->hourlyTask();
            $this->dailyTask();
        } else {
            set_time_limit(1800);
            $this->hourlyTask();
        }

        $this->finalize();
    }

    private function hourlyTask()
    {
        $this->hourlyRankingPositionCheckLastHour();
        $this->hourlyMerge();
        $this->state->isActive = false;
        $this->state->update();

        $this->hourlyRankingPosition();
        $this->hourlyMemberRankingUpdate();
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
        $this->rankingPositionHourPersistence->persistStorageFileToDb();
    }

    private function hourlyMemberRankingUpdate()
    {
        $this->hourlyMemberRanking->update();
    }

    private function dailyTask()
    {
        $this->state->isDailyTaskActive = true;
        $this->state->update();

        try {
            /** @var DailyUpdateCronService $updater */
            $updater = app(DailyUpdateCronService::class);
            $updater->update();
        } catch (\Throwable $e) {
            $this->state->isDailyTaskActive = false;
            throw $e;
        }

        $this->state->isDailyTaskActive = false;
        $this->state->update();
    }

    function finalize(): void
    {
        $this->sitemap->generate();
    }
}
