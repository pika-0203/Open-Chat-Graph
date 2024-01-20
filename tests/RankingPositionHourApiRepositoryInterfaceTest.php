<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Models\Repositories\RankingPosition\RankingPositionHourApiRepositoryInterface;

class RankingPositionHourApiRepositoryInterfaceTest extends TestCase
{
    public function test()
    {
        /**
         * @var RankingPositionHourApiRepositoryInterface $repo
         */
        $repo = app(RankingPositionHourApiRepositoryInterface::class);

        $result = $repo->getLatestRanking(
            'v_Dj23i8M16eolHfkEh8SmhbeG9pYsp7dF8IP-vSyNs9Bf3NlOQ528Sjs7Q',
            17,
            new DateTime('2024-01-19 16:30:00')
        );

        debug($result);

        $this->assertTrue(true);
    }
}
