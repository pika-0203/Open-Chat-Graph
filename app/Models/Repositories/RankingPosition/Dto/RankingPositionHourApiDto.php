<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition\Dto;

class RankingPositionHourApiDto
{
    public int $rising_total_count;
    public int $rising_all_total_count;
    public int $ranking_total_count;
    public int $ranking_all_total_count;
    public int|false $member = false;
    public int|false $rising_position = false;
    public int|false $rising_all_position = false;
    public int|false $ranking_position = false;
    public int|false $ranking_all_position = false;
}
