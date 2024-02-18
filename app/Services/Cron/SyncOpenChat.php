<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Services\Admin\AdminTool;
use App\Services\Cron\CronJson\SyncOpenChatState;
use App\Services\OpenChat\OpenChatApiDbMergerWithParallelDownloader;
use App\Services\DailyUpdateCronService;
use App\Services\OpenChat\OpenChatApiDataParallelDownloader;
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
        private OpenChatApiDbMergerWithParallelDownloader $merger,
        private SitemapGenerator $sitemap,
        private RankingPositionHourPersistence $rankingPositionHourPersistence,
        private RankingPositionHourPersistenceLastHourChecker $rankingPositionHourChecker,
        private UpdateHourlyMemberRankingService $hourlyMemberRanking,
        private UpdateHourlyMemberColumnService $hourlyMemberColumn
    ) {
        set_exception_handler($this->exceptionHandler(...));
    }

    private function exceptionHandler(\Throwable $e)
    {
        OpenChatApiDataParallelDownloader::enableKillFlag();
        AdminTool::sendLineNofity($e->__toString());
        addCronLog($e->__toString());
    }

    function handle()
    {
        $this->init();

        if (isDailyUpdateTime()) {
            $this->dailyTask();
        } else if ($this->isFailedDailyUpdate()) {
            addCronLog('Retry dailyTask');
            AdminTool::sendLineNofity('Retry dailyTask');
            OpenChatDailyCrawling::enableKillFlag();
            sleep(3);
            $this->dailyTask();
        } else {
            $this->hourlyTask();
        }

        $this->finalize();
    }

    private function isFailedDailyUpdate(): bool
    {
        return isDailyUpdateTime(new \DateTime('-2 hour'), nowStart: new \DateTime('-1day'), nowEnd: new \DateTime('-1day'))
            && $this->state->isDailyTaskActive;
    }

    function handleHalfHourCheck()
    {
        if ($this->state->isHourlyTaskActive) {
            addCronLog('Retry hourlyTask');
            OpenChatApiDataParallelDownloader::enableKillFlag();
            OpenChatDailyCrawling::enableKillFlag();
            sleep(3);
            $this->handle();
            return;
        }

        if (!$this->rankingPositionHourChecker->isLastHourPersistenceCompleted()) {
            addCronLog('Retry position perisistance');
            $this->hourlyRankingPosition();
            $this->hourlyMemberRankingUpdate();
            return;
        }
    }

    private function init()
    {
        checkLineSiteRobots();

        if ($this->state->isHourlyTaskActive) {
            AdminTool::sendLineNofity('SyncOpenChat: hourlyTask is active');
        }

        if ($this->state->isDailyTaskActive) {
            AdminTool::sendLineNofity('SyncOpenChat: dailyTask is active');
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
        $this->merger->fetchOpenChatApiRankingAll();
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
        $updater->update();

        $this->state->isDailyTaskActive = false;
        $this->state->update();
    }

    function finalize(): void
    {
        $this->sitemap->generate();
    }
}
