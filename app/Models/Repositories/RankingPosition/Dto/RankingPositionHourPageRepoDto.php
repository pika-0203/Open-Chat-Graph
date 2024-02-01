<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition\Dto;

class RankingPositionHourPageRepoDto
{
    /** @var string[] Y-m-d H:i:s */
    public array $time = [];

    /** @var int[] */
    public array $position = [];

    /** @var int[] */
    public array $totalCount = [];

    /** @var int[] */
    public array $member = [];
}
