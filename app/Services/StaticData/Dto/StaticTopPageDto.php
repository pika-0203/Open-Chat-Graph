<?php

declare(strict_types=1);

namespace App\Services\StaticData\Dto;

class StaticTopPageDto
{
    public array $hourlyList;
    public array $dailyList;
    public array $weeklyList;
    public array $popularList;
    public \DateTime $hourlyUpdatedAt;
    public \DateTime $dailyUpdatedAt;
}