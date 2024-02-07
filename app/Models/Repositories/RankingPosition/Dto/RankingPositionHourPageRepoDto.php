<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition\Dto;

class RankingPositionHourPageRepoDto
{
    /** @var string[] H:i */
    public array $time = [];

    /** @var (int|null)[] */
    public array $position = [];

    /** @var (int|null)[] */
    public array $totalCount = [];

    /** @var (int|null)[] */
    public array $member = [];

    /** @var string Y-m-d H:i:s */
    public string $firstTime;
}
