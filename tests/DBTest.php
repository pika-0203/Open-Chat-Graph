<?php

declare(strict_types=1);

use App\Config\AppConfig;
use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourMemberDto;
use App\Models\SQLite\SQLiteRankingPositionHour;
use App\Services\StaticData\StaticTopPageDataGenerator;
use PHPUnit\Framework\TestCase;

class DBTest extends TestCase
{
    public function test()
    {
        app(StaticTopPageDataGenerator::class)->getTopPageDataFromDB();
        $this->assertTrue(true);
    }
}
