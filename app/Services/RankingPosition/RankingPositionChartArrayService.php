<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Services\RankingPosition\Dto\RankingPositionChartDto;
use App\Models\Repositories\RankingPosition\Dto\RankingPositionPageRepoDto;
use App\Models\Repositories\RankingPosition\RankingPositionPageRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsPageRepositoryInterface;

class RankingPositionChartArrayService
{
    private const SUBSTR_YMD_LEN = 10;
    private const SUBSTR_HI_OFFSET = 11;
    private const SUBSTR_HI_LEN = 5;

    function __construct(
        private RankingPositionPageRepositoryInterface $rankingPositionPageRepository,
        private StatisticsPageRepositoryInterface $statisticsPageRepository,
    ) {
    }

    function getRankingPositionChartArray(int $open_chat_id, int $category): RankingPositionChartDto
    {
        $repoDto = $this->rankingPositionPageRepository->getDailyRankingPositionTimeAsc($open_chat_id, $category);
        if (!$repoDto) {
            return $this->getStatsChartArrayWithoutPosition($open_chat_id);
        }

        return $this->buildRankingPositionChartArray($open_chat_id, $repoDto);
    }

    function getRisingPositionChartArray(int $open_chat_id, int $category): RankingPositionChartDto
    {
        $repoDto = $this->rankingPositionPageRepository->getDailyRisingPositionTimeAsc($open_chat_id, $category);
        if (!$repoDto) {
            return $this->getStatsChartArrayWithoutPosition($open_chat_id);
        }

        return $this->buildRankingPositionChartArray($open_chat_id, $repoDto);
    }

    function getStatsChartArrayWithoutPosition(int $open_chat_id): RankingPositionChartDto
    {
        return $this->buildRankingPositionChartArray($open_chat_id, new RankingPositionPageRepoDto);
    }

    private function buildRankingPositionChartArray(int $open_chat_id, RankingPositionPageRepoDto $repoDto): RankingPositionChartDto
    {
        $memberStats = $this->statisticsPageRepository->getDailyMemberStatsDateAsc($open_chat_id);
        if (!$memberStats) {
            return false;
        }

        return $this->generateChartArray(
            $this->generateDateArray(
                $memberStats[0]['date'],
                $memberStats[count($memberStats) - 1]['date']
            ),
            $memberStats,
            $repoDto
        );
    }

    /**  
     *  @param string $firstDate `Y-m-d`
     *  @return string[]
     */
    private function generateDateArray(string $firstDate, string $endDate): array
    {
        $first = new \DateTime($firstDate);

        $interval = $first->diff(new \DateTime($endDate))->days;
        $dateArray = [];
        $i = 0;

        while ($i <= $interval) {
            $dateArray[] = $first->format('Y-m-d');
            $first->modify('+1 day');
            $i++;
        }

        return $dateArray;
    }

    /**
     * @param string[] $dateArray
     * @param array{ date:string, member:int }[] $memberStats
     */
    private function generateChartArray(array $dateArray, array $memberStats, RankingPositionPageRepoDto $repoDto): RankingPositionChartDto
    {
        $dto = new RankingPositionChartDto;

        $getMemberStatsCurDate = fn (int $key): string => $memberStats[$key]['date'] ?? '';
        $getRepoDtoCurDate = fn (int $key): string => isset($repoDto->time[$key]) ? substr($repoDto->time[$key], 0, self::SUBSTR_YMD_LEN) : '';

        $curKeyMemberStats = 0;
        $curKeyRepoDto = 0;
        $repoDtoArrayCount = count($repoDto->time);
        $repoDtoCurDate = $getRepoDtoCurDate(0);
        $memberStatsCurDate = $getMemberStatsCurDate(0);

        foreach ($dateArray as $date) {
            $matchMemberStats = $memberStatsCurDate === $date;
            $matchRepoDto = $repoDtoCurDate === $date;

            $dto->addValue(
                $date,
                $matchMemberStats ? $memberStats[$curKeyMemberStats]['member'] : null,
                $matchRepoDto ? substr($repoDto->time[$curKeyRepoDto], self::SUBSTR_HI_OFFSET, self::SUBSTR_HI_LEN) : null,
                $matchRepoDto ? $repoDto->position[$curKeyRepoDto] : ($curKeyRepoDto > 0 && $curKeyRepoDto < $repoDtoArrayCount ? 0 : null),
                $matchRepoDto ? $repoDto->totalCount[$curKeyRepoDto] : null,
            );

            if ($matchMemberStats) {
                $curKeyMemberStats++;
                $memberStatsCurDate = $getMemberStatsCurDate($curKeyMemberStats);
            }

            if ($matchRepoDto) {
                $curKeyRepoDto++;
                $repoDtoCurDate = $getRepoDtoCurDate($curKeyRepoDto);
            }
        }

        return $dto;
    }
}
