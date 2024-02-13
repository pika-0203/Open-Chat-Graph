<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Services\Admin\AdminTool;
use App\Services\Cron\CronJson\SyncOpenChatState;
use App\Services\OpenChat\OpenChatApiDbMerger;
use App\Services\DailyUpdateCronService;
use App\Services\OpenChat\OpenChatDailyCrawling;
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
            $this->hourlyTask();
            $this->dailyTask();
        } else if (isDailyUpdateTime(new \DateTime('-2 hour')) && $this->state->isDailyTaskActive) {
            OpenChatDailyCrawling::enableKillFlag();
            $this->hourlyTask();
            $this->dailyTask();
        } else {
            $this->hourlyTask();
        }

        $this->finalize();
    }

    function handleHalfHourCheck()
    {
        if ($this->state->isHourlyTaskActive) {
            OpenChatApiDbMerger::enableKillFlag();
            addCronLog('Retry hourlyTask');
            AdminTool::sendLineNofity('Retry hourlyTask');
            $this->handle();
            return;
        }

        if (!$this->rankingPositionHourChecker->isLastHourPersistenceCompleted()) {
            addCronLog('Retry position perisistance');
            AdminTool::sendLineNofity('Retry position perisistance');
            $this->hourlyRankingPosition();
            $this->hourlyMemberRankingUpdate();
            return;
        }
    }

    function hourlyTask()
    {
        set_time_limit(1620);

        $this->hourlyMerge();

        $this->state->isHourlyTaskActive = false;
        $this->state->update();

        $this->hourlyRankingPosition();
        $this->hourlyMemberRankingUpdate();
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

    function dailyTask()
    {
        $this->state->isDailyTaskActive = true;
        $this->state->update();

        $this->hourlyTask();

        set_time_limit(3600);
        try {
            /** @var DailyUpdateCronService $updater */
            $updater = app(DailyUpdateCronService::class);
            OpenChatDailyCrawling::disableKillFlag();
            $updater->update();
        } catch (\Throwable $e) {
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
