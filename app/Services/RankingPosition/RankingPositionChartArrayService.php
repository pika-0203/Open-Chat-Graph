<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Services\RankingPosition\Dto\RankingPositionChartDto;
use App\Models\Repositories\RankingPosition\Dto\RankingPositionPageRepoDto;
use App\Models\Repositories\RankingPosition\RankingPositionPageRepositoryInterface;

class RankingPositionChartArrayService
{
    private const SUBSTR_YMD_LEN = 10;
    private const SUBSTR_HI_OFFSET = 11;
    private const SUBSTR_HI_LEN = 5;
    private const START_DATE = '2024-01-19';

    function __construct(
        private RankingPositionPageRepositoryInterface $rankingPositionPageRepository,
    ) {
    }

    function getRankingPositionChartArray(int $open_chat_id, int $category, \DateTime $startDate, \DateTime $endDate): RankingPositionChartDto|false
    {
        $repoDto = $this->rankingPositionPageRepository->getDailyRankingPositionTimeAsc($open_chat_id, $category);
        if (!$repoDto) {
            return new RankingPositionPageRepoDto;
        }

        return $this->buildRankingPositionChartArray($repoDto, $startDate, $endDate);
    }

    function getRisingPositionChartArray(int $open_chat_id, int $category, \DateTime $startDate, \DateTime $endDate): RankingPositionChartDto|false
    {
        $repoDto = $this->rankingPositionPageRepository->getDailyRisingPositionTimeAsc($open_chat_id, $category);
        if (!$repoDto) {
            return new RankingPositionPageRepoDto;
        }

        return $this->buildRankingPositionChartArray($repoDto, $startDate, $endDate, true);
    }

    private function buildRankingPositionChartArray(
        RankingPositionPageRepoDto $repoDto,
        \DateTime $startDate,
        \DateTime $endDate,
        bool $includeTime = false
    ): RankingPositionChartDto|false {
        return $this->generateChartArray(
            $this->generateDateArray($startDate, $endDate),
            $repoDto,
            $includeTime
        );
    }

    /**  
     *  @return string[]
     */
    private function generateDateArray(\DateTime $startDate, \DateTime $endDate): array
    {
        $first = new \DateTime($startDate->format('Y-m-d'));
        $interval = $first->diff($endDate)->days;
        if ($interval < 8) {
            $mod = 7 - $interval;
            $first->modify("-{$mod} day");
            $interval = $first->diff($endDate)->days;
        }

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
    private function generateChartArray(array $dateArray, RankingPositionPageRepoDto $repoDto, bool $includeTime): RankingPositionChartDto
    {
        $dto = new RankingPositionChartDto;

        $getRepoDtoCurDate = fn (int $key): string => isset($repoDto->time[$key]) ? substr($repoDto->time[$key], 0, self::SUBSTR_YMD_LEN) : '';
        $getIsBeforeStartDate = fn (string $date) => strtotime($date) < strtotime(self::START_DATE);

        $curKeyRepoDto = 0;
        $repoDtoCurDate = $getRepoDtoCurDate(0);
        $isBeforeStartDate = true;

        foreach ($dateArray as $date) {
            if ($isBeforeStartDate) {
                $isBeforeStartDate = $getIsBeforeStartDate($date);
            }

            $matchRepoDto = $repoDtoCurDate === $date;

            $dto->addValue(
                false,
                false,
                $matchRepoDto && $includeTime ? substr($repoDto->time[$curKeyRepoDto], self::SUBSTR_HI_OFFSET, self::SUBSTR_HI_LEN) : null,
                $matchRepoDto ? $repoDto->position[$curKeyRepoDto] : ($date === $repoDto->nextDate || $isBeforeStartDate || !$curKeyRepoDto ? null : 0),
                $matchRepoDto ? $repoDto->totalCount[$curKeyRepoDto] : null,
            );

            if ($matchRepoDto) {
                $curKeyRepoDto++;
                $repoDtoCurDate = $getRepoDtoCurDate($curKeyRepoDto);
            }
        }

        return $dto;
    }
}
