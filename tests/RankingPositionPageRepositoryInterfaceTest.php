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


        $this->assertTrue(true);
    }
}
