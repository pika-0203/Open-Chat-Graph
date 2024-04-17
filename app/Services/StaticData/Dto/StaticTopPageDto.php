<?php

declare(strict_types=1);

namespace App\Services\StaticData\Dto;

class StaticTopPageDto
{
    public array $hourlyList;
    public array $dailyList;
    public array $weeklyList;
    public array $popularList;

    /** @var array{ id:int, name:string, img_url:string, description:string, member:int, emblem:int, category:int, time:string }[] $recentCommentList */
    public array $recentCommentList = [];

    /** @var array{ hour:string[],hour24:string[] } $recommendList */
    public array $recommendList;

    public \DateTime $hourlyUpdatedAt;
    public \DateTime $dailyUpdatedAt;
}
