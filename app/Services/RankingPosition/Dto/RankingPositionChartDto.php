<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Dto;

class RankingPositionChartDto
{
    /** @var string[] Y-m-d */
    public array $date = [];

    /** @var (int|null)[] */
    public array $member = [];

    /** @var (string|null)[] Y-m-d H:i:s */
    public array $time = [];

    /** @var (int|null)[] */
    public array $position = [];

    /** @var (int|null)[] */
    public array $totalCount = [];

    function addValue(string|false $date, int|null|false $member, string|null $time, int|null $position, int|null $totalCount)
    {
        if ($date !== false) $this->date[] = $date;
        if ($member !== false) $this->member[] = $member;
        $this->time[] = $time;
        $this->position[] = $position;
        $this->totalCount[] = $totalCount;
    }
}
