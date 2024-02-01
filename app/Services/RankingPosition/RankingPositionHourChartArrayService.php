<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Services\RankingPosition\Dto\RankingPositionChartDto;
use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourPageRepoDto;
use App\Models\Repositories\RankingPosition\RankingPositionHourPageRepositoryInterface;

class RankingPositionChartArrayService
{
    private const SUBSTR_YMD_LEN = 10;
    private const SUBSTR_HI_OFFSET = 11;
    private const SUBSTR_HI_LEN = 5;

    function __construct(
        private RankingPositionHourPageRepositoryInterface $rankingPositionHourPageRepository,
    ) {
    }

    function getRankingPositionHourChartArray(string $emid, int $category): RankingPositionChartDto|false
    {
        $repoDto = $this->rankingPositionHourPageRepository->getHourRankingPositionTimeAsc($emid, $category);
        if (!$repoDto) {
            return false;
        }

        return $this->buildRankingPositionChartArray($repoDto);
    }

    function getRisingPositionHourChartArray(string $emid, int $category): RankingPositionChartDto|false
    {
        $repoDto = $this->rankingPositionHourPageRepository->getHourRisingPositionTimeAsc($emid, $category);
        if (!$repoDto) {
            return false;
        }

        return $this->buildRankingPositionChartArray($repoDto);
    }

    private function buildRankingPositionChartArray(RankingPositionHourPageRepoDto $repoDto): RankingPositionChartDto|false
    {
        return $this->generateChartArray(
            $this->generateDateArray(
                $repoDto->time[0],
                $repoDto->time[count(($repoDto->time)) - 1]
            ),
            $repoDto
        );
    }

    /**  
     *  @return string[]
     */
    private function generateDateArray(string $firstTime, string $endTime): array
    {
        $first = new \DateTime($firstTime);

        $interval = $first->diff(new \DateTime($endTime))->h;
        $dateArray = [];
        $i = 0;

        while ($i <= $interval) {
            $dateArray[] = $first->format('Y-m-d H:i:s');
            $first->modify('+1 hour');
            $i++;
        }

        return $dateArray;
    }

    /**
     * @param string[] $dateArray
     * @param array{ date:string, member:int }[]
     */
    private function generateChartArray(array $dateArray, RankingPositionHourPageRepoDto $repoDto): RankingPositionChartDto
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
