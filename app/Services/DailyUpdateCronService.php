<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\AppConfig;
use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepository;
use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Services\OpenChat\OpenChatDailyCrawling;
use App\Services\OpenChat\SubCategory\OpenChatSubCategorySynchronizer;
use App\Services\OpenChat\Updater\MemberColumnUpdater;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use App\Services\RankingPosition\RankingPositionDailyUpdater;

class DailyUpdateCronService
{
    private string $date;

    function __construct(
        private RankingPositionDailyUpdater $rankingPositionDailyUpdater,
        private OpenChatDailyCrawling $openChatDailyCrawling,
        private OpenChatRepositoryInterface $openChatRepository,
        private StatisticsRepositoryInterface $statisticsRepository,
        private UpdateDailyRankingService $updateRankingService,
        private MemberColumnUpdater $memberColumnUpdater,
        private OpenChatSubCategorySynchronizer $openChatSubCategorySynchronizer,
    ) {
        $this->date = OpenChatServicesUtility::getCronModifiedStatsMemberDate();
    }

    /**
     * @return int[]
     */
    function getTargetOpenChatIdArray(): array
    {
        $ocDbIdArray = $this->openChatRepository->getOpenChatIdAllByCreatedAtDate($this->date);
        $statsDbIdArray = $this->statisticsRepository->getOpenChatIdArrayByDate($this->date);

        $filteredIdArray = array_diff($ocDbIdArray, $statsDbIdArray);

        $memberChangeWithinLastWeekIdArray = $this->statisticsRepository->getMemberChangeWithinLastWeekCacheArray($this->date);

        return array_filter($filteredIdArray, fn (int $id) => in_array($id, $memberChangeWithinLastWeekIdArray));
    }

    function update(?\Closure $crawlingEndFlag = null): void
    {
        $this->rankingPositionDailyUpdater->updateYesterdayDailyDb();

        $outOfRankId = $this->getTargetOpenChatIdArray();

        addCronLog('openChatCrawling start: ' . count($outOfRankId));
        
        // 開発環境の場合、更新制限をかける
        if (AppConfig::$isDevlopment ?? false) {
            $limit = AppConfig::$developmentEnvUpdateLimit['DailyUpdateCronService'] ?? 1;
            $outOfRankIdCount = count($outOfRankId);
            $outOfRankId = array_slice($outOfRankId, 0, $limit);
            addCronLog("Development environment. Update limit: {$limit} / {$outOfRankIdCount}");
        }

        $result = $this->openChatDailyCrawling->crawling($outOfRankId);

        addCronLog('openChatCrawling done: ' . $result);
        unset($outOfRankId);
        OpenChatDataForUpdaterWithCacheRepository::clearCache();

        if ($crawlingEndFlag)
            $crawlingEndFlag();

        addCronLog('syncSubCategoriesAll start');
        $categoryResult = $this->openChatSubCategorySynchronizer->syncSubCategoriesAll();
        addCronLog('syncSubCategoriesAll done: ' . count($categoryResult));

        $this->updateRankingService->update($this->date);
    }
}
