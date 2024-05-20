<?php

declare(strict_types=1);

namespace App\Services\StaticData\Dto;

class StaticRecommendPageDto
{
    public string $hourlyUpdatedAt;
    /** @var array $tagRecordCounts ['タグ名' => int] */
    public array $tagRecordCounts;
    public int $tagCount;
}
