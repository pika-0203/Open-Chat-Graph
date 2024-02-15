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
use App\Services\UpdateHourlyMemberColumnService;
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
        private UpdateHourlyMemberColumnService $hourlyMemberColumn
    ) {
    }

    function handle()
    {
        if (isDailyUpdateTime()) {
            $this->hourlyTask();
            $this->dailyTask();
        } else if (
            isDailyUpdateTime(new \DateTime('-2 hour'), nowStart: new \DateTime('-1day'))
            && $this->state->isDailyTaskActive
        ) {
            addCronLog('Retry dailyTask');
            AdminTool::sendLineNofity('Retry dailyTask');
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
        $this->state->isHourlyTaskActive = true;
        $this->state->update();

        $this->hourlyMerge();

        $this->state->isHourlyTaskActive = false;
        $this->state->update();

        if (!$this->rankingPositionHourChecker->isLastHourPersistenceCompleted()) {
            $this->hourlyRankingPosition();
        }
        
        $this->hourlyMemberRankingUpdate();
    }

    private function hourlyMerge()
    {
        set_time_limit(1620);
        
        OpenChatApiDbMerger::enableKillFlag();
        sleep(3);
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
        $this->hourlyMemberColumn->update();
        $this->hourlyMemberRanking->update();
    }

    function dailyTask()
    {
        $this->state->isDailyTaskActive = true;
        $this->state->update();

        $this->hourlyTask();

        set_time_limit(5400);

        /** @var DailyUpdateCronService $updater */
        $updater = app(DailyUpdateCronService::class);
        OpenChatDailyCrawling::enableKillFlag();
        sleep(3);
        OpenChatDailyCrawling::disableKillFlag();
        $updater->update();

        $this->state->isDailyTaskActive = false;
        $this->state->update();
    }

    function finalize(): void
    {
        $this->sitemap->generate();
    }
}
