<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
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
        private UpdateOpenChatRepositoryInterface $updateRepository,
        private StatisticsRepositoryInterface $statisticsRepository,
        private UpdateRankingService $updateRankingService,
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
        $ocDbIdArray = $this->updateRepository->getOpenChatIdAll();
        $statsDbIdArray = $this->statisticsRepository->getOpenChatIdArrayByDate($this->date);

        $filteredIdArray = array_diff($ocDbIdArray, $statsDbIdArray);

        $memberChangeWithinLastWeekIdArray = $this->statisticsRepository->getMemberChangeWithinLastWeekCacheArray($this->date);

        return array_filter($filteredIdArray, fn (int $id) => in_array($id, $memberChangeWithinLastWeekIdArray));
    }

    function update(): void
    {
        $this->rankingPositionDailyUpdater->updateYesterdayDailyDb();

        $outOfRankId = $this->getTargetOpenChatIdArray();

        addCronLog('openChatCrawling start: ' . count($outOfRankId));
        $result = $this->openChatDailyCrawling->crawling($outOfRankId);
        addCronLog('openChatCrawling done: ' . $result);
        unset($outOfRankId);

        $this->updateRankingService->update($this->date);

        addCronLog('syncSubCategoriesAll start');
        $categoryResult = $this->openChatSubCategorySynchronizer->syncSubCategoriesAll();
        addCronLog('syncSubCategoriesAll done: ' . count($categoryResult));
    }
}
