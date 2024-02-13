<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepositoryInterface;
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
        private OpenChatDataForUpdaterWithCacheRepositoryInterface $openChatDataWithCache,
        private UpdateRankingService $updateRankingService,
        private MemberColumnUpdater $memberColumnUpdater,
        private OpenChatSubCategorySynchronizer $openChatSubCategorySynchronizer,
    ) {
        $this->date = OpenChatServicesUtility::getCronModifiedStatsMemberDate();
    }

    /**
     * @return array{ 0: array{ open_chat_id: int, member: int }[], 1: int[] }
     */
    private function getTargetOpenChatIdArray(): array
    {
        $ocDbIdArray = $this->updateRepository->getOpenChatIdAll();
        $statsDbIdMemberArray = $this->statisticsRepository->getOpenChatIdArrayByDate($this->date);

        $statsDbIdArray = array_column($statsDbIdMemberArray, 'open_chat_id');
        $filteredIdArray = array_filter($ocDbIdArray, fn (int $id) => !in_array($id, $statsDbIdArray));

        $memberChangeWithinLastWeekIdArray = $this->statisticsRepository->getMemberChangeWithinLastWeekCacheArray($this->date);

        return [
            $statsDbIdMemberArray,
            array_filter($filteredIdArray, fn (int $id) => in_array($id, $memberChangeWithinLastWeekIdArray))
        ];
    }

    function update(): void
    {
        $this->rankingPositionDailyUpdater->updateYesterdayDailyDb();

        [$inRankIdMember, $outOfRankId] = $this->getTargetOpenChatIdArray();

        $this->memberColumnUpdater->updateMemberColumn($inRankIdMember);
        unset($inRankIdMember);

        addCronLog('openChatCrawling start: ' . count($outOfRankId));
        $result = $this->openChatDailyCrawling->crawling($outOfRankId);
        addCronLog('openChatCrawling done: ' . $result);
        unset($outOfRankId);

        $this->updateRankingService->update($this->date);
    }
}
