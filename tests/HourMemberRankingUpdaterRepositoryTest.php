<?php

use App\Models\RankingPositionDB\Repositories\HourMemberRankingUpdaterRepository;
use App\Services\OpenChat\Enum\RankingType;
use PHPUnit\Framework\TestCase;

class HourMemberRankingUpdaterRepositoryTest extends TestCase
{
    public function testinsertDailyRankingPosition()
    {
        /**
         * @var HourMemberRankingUpdaterRepository $repo
         */
        $repo = app(HourMemberRankingUpdaterRepository::class);

        $result = $repo->updateHourRankingTable(new DateTime('2024-02-17 09:30:00'), [3,333,3232,43242,5534,534,5454]);

        debug($result);

        $this->assertTrue(true);
    }
}
