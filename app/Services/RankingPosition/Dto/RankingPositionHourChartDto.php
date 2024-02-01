<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Dto;

class RankingPositionHourChartDto
{
    /** @var string[] H:i */
    public array $time = [];

    /** @var (int|null)[] */
    public array $member = [];

    /** @var (int|null)[] */
    public array $position = [];

    /** @var (int|null)[] */
    public array $totalCount = [];

    function addValue(string $time, int|null $member, int|null $position, int|null $totalCount)
    {
        $this->time[] = $time;
        $this->member[] = $member;
        $this->position[] = $position;
        $this->totalCount[] = $totalCount;
    }
}
