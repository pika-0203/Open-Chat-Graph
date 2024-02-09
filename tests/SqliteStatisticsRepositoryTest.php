<?php

declare(strict_types=1);

use App\Models\SQLite\Repositories\Statistics\SqliteStatisticsRepository;
use PHPUnit\Framework\TestCase;
use Shadow\DB;

class SqliteStatisticsRepositoryTest extends TestCase
{
    private SqliteStatisticsRepository $inst;

    function test()
    {
        $this->inst = app(SqliteStatisticsRepository::class);

        $res = $this->inst->getMemberChangeWithinLastWeekCacheArray('2024-02-08');

        debug(in_array(139976, $res));

        $this->assertIsBool(true);
    }
}
