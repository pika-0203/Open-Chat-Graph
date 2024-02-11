<?php

declare(strict_types=1);

use App\Models\SQLite\Repositories\RankingPosition\SqliteHourMemberRankingUpdaterRepository;
use PHPUnit\Framework\TestCase;

class SqliteHourMemberRankingUpdaterRepositoryTest extends TestCase
{
    private SqliteHourMemberRankingUpdaterRepository $instance;

    public function test()
    {
        $this->instance = app(SqliteHourMemberRankingUpdaterRepository::class);

        $result = $this->instance->updateHourRankingTable(new \DateTime('2024-02-10 15:30:00'));

        debug($result);

        $this->assertTrue(true);
    }
}
