<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Services\RankingPosition\Dto\RankingPositionChartDto;
use App\Models\Repositories\RankingPosition\Dto\RankingPositionPageRepoDto;
use App\Models\Repositories\RankingPosition\RankingPositionPageRepositoryInterface;

class RankingPositionChartArrayService
{
    function __construct(
        private RankingPositionPageRepositoryInterface $rankingPositionPageRepository
    ) {
    }

    function getRankingPositionChartArray(int $open_chat_id, int $category): RankingPositionChartDto
    {
        $repoDto = $this->rankingPositionPageRepository->getDailyRankingPositionTimeAsc($open_chat_id, $category);
        return $repoDto
            ? $this->buildRankingPositionChartArray($open_chat_id, $repoDto)
            : new RankingPositionChartDto;
    }

    function getRisingPositionChartArray(int $open_chat_id, int $category): RankingPositionChartDto
    {
        $repoDto = $this->rankingPositionPageRepository->getDailyRisingPositionTimeAsc($open_chat_id, $category);
        return $repoDto
            ? $this->buildRankingPositionChartArray($open_chat_id, $repoDto)
            : new RankingPositionChartDto;
    }

    private function buildRankingPositionChartArray(int $open_chat_id, RankingPositionPageRepoDto $repoDto): RankingPositionChartDto
    {
        $firstTime = $this->rankingPositionPageRepository->getFirstTime($open_chat_id);
        return $firstTime
            ? $this->generateChartArray($this->generateDateArray($firstTime), $repoDto)
            : new RankingPositionChartDto;
    }

    /**
     * @param string[] $dateArray
     */
    private function generateChartArray(array $dateArray, RankingPositionPageRepoDto $repoDto): RankingPositionChartDto
    {
        $dto = new RankingPositionChartDto;

        $cursor = 0;
        $getCurrentDate = fn ($key) => substr($repoDto->time[$key] ?? '', 0, 10);
        $currentDate = $getCurrentDate(0);

        foreach ($dateArray as $time) {
            if ($currentDate !== $time) {
                $dto->addValue($time, 0, 0);
                continue;
            }

            $dto->addValue(
                substr($repoDto->time[$cursor], 0, 16),
                $repoDto->position[$cursor],
                $repoDto->totalCount[$cursor]
            );

            $cursor++;
            $currentDate = $getCurrentDate($cursor);
        }

        return $dto;
    }

    /**  
     *  @return string[]
     */
    private function generateDateArray(\DateTime $firstTime): array
    {
        $currentTime = new \DateTime($firstTime->format('Y-m-d'));
        $interval = $firstTime->diff(new \DateTime)->days;
        $dateArray = [];
        
        for ($i = 0; $i <= $interval; $i++) {
            $dateArray[] = $currentTime->format('Y-m-d');
            $currentTime->modify('+1 day');
        }

        return $dateArray;
    }
}
