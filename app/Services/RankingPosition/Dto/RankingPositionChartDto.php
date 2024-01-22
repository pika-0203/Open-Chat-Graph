<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Dto;

class RankingPositionChartDto
{
    /** @var string[] Y-m-d H:i:s or Y-m-d */
    public array $time = [];

    /** @var int[] */
    public array $position = [];

    /** @var int[] */
    public array $totalCount = [];

    function addValue(string $time, int $position, int $totalCount)
    {
        $this->time[] = $time;
        $this->position[] = $position;
        $this->totalCount[] = $totalCount;
    }
}
