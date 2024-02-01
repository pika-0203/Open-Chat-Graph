<?php

declare(strict_types=1);

use App\Models\Repositories\RankingPosition\RankingPositionHourPageRepositoryInterface;
use PHPUnit\Framework\TestCase;

class RankingPositionHourPageRepositoryInterfaceTest extends TestCase
{
    private RankingPositionHourPageRepositoryInterface $instance;

    public function test()
    {
        $this->instance = app(RankingPositionHourPageRepositoryInterface::class);

        $result = $this->instance->getDailyRankingPositionTimeAsc(192, 8);
        debug($result);
        $this->assertTrue(true);
    }

    public function testgetFirstTime()
    {
        $this->instance = app(RankingPositionHourPageRepositoryInterface::class);

        $result = $this->instance->getFirstTime(192);
        debug($result);
        $this->assertTrue(true);
    }
}
