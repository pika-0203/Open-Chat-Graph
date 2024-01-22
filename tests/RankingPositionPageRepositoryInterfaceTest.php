<?php

declare(strict_types=1);

use App\Models\Repositories\RankingPosition\RankingPositionPageRepositoryInterface;
use PHPUnit\Framework\TestCase;

class RankingPositionPageRepositoryInterfaceTest extends TestCase
{
    private RankingPositionPageRepositoryInterface $instance;

    public function test()
    {
        $this->instance = app(RankingPositionPageRepositoryInterface::class);

        $result = $this->instance->getDailyRankingPositionTimeAsc(192, 8);
        debug($result);
        $this->assertTrue(true);
    }

    public function testgetFirstTime()
    {
        $this->instance = app(RankingPositionPageRepositoryInterface::class);

        $result = $this->instance->getFirstTime(192);
        debug($result);
        $this->assertTrue(true);
    }
}
