<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Services\RankingPosition\Dto\RankingPositionHourChartDto;
use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourPageRepoDto;
use App\Models\Repositories\RankingPosition\RankingPositionHourPageRepositoryInterface;

class RankingPositionHourChartArrayService
{
    private const INTERVAL_HOUR = 24;

    function __construct(
        private RankingPositionHourPageRepositoryInterface $rankingPositionHourPageRepository,
    ) {
    }

    function getRankingPositionHourChartArray(string $emid, int $category): RankingPositionHourChartDto
    {
        $repoDto = $this->rankingPositionHourPageRepository->getHourRankingPositionTimeAsc($emid, $category, self::INTERVAL_HOUR);
        if (!$repoDto) {
            return new RankingPositionHourChartDto;
        }

        return $this->buildRankingPositionChartArray($repoDto);
    }

    function getRisingPositionHourChartArray(string $emid, int $category): RankingPositionHourChartDto
    {
        $repoDto = $this->rankingPositionHourPageRepository->getHourRisingPositionTimeAsc($emid, $category, self::INTERVAL_HOUR);
        if (!$repoDto) {
            return new RankingPositionHourChartDto;
        }

        return $this->buildRankingPositionChartArray($repoDto);
    }

    private function buildRankingPositionChartArray(RankingPositionHourPageRepoDto $repoDto): RankingPositionHourChartDto
    {
        return $this->generateChartArray($this->generateTimeArray($repoDto->firstTime), $repoDto);
    }

    /**  
     *  @return string[]
     */
    private function generateTimeArray(string $firstTime): array
    {
        $first = new \DateTime($firstTime);

        for ($i = 0; $i <= self::INTERVAL_HOUR; $i++) {
            $timeArray[] = $first->format('Y-m-d H:i:s');
            $first->modify('+1 hour');
        }

        return $timeArray;
    }

    /**
     * @param string[] $timeArray
     */
    private function generateChartArray(array $timeArray, RankingPositionHourPageRepoDto $repoDto): RankingPositionHourChartDto
    {
        $dto = new RankingPositionHourChartDto;

        $getRepoDtoCurTime = fn (int $key): string => isset($repoDto->time[$key]) ? $repoDto->time[$key] : '';

        $curKeyRepoDto = 0;
        $repoDtoArrayCount = count($repoDto->time);
        $repoDtoCurTime = $getRepoDtoCurTime(0);

        foreach ($timeArray as $time) {
            $timeStr = (new \DateTime($time))->format('m/d H:i');

            if ($repoDtoCurTime !== $time) {
                $dto->addValue(
                    $timeStr,
                    null,
                    $curKeyRepoDto > 0 && $curKeyRepoDto < $repoDtoArrayCount ? 0 : null,
                    null,
                );

                continue;
            }

            $dto->addValue(
                $timeStr,
                $repoDto->member[$curKeyRepoDto],
                $repoDto->position[$curKeyRepoDto],
                $repoDto->totalCount[$curKeyRepoDto],
            );

            $curKeyRepoDto++;
            $repoDtoCurTime = $getRepoDtoCurTime($curKeyRepoDto);
        }

        return $dto;
    }
}
