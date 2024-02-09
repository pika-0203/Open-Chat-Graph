<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Shadow\DB;
use App\Models\GCE\DBGce;
use App\Models\GCE\GceDbTableSynchronizer;
use App\Models\SQLite\Repositories\Statistics\SqliteStatisticsRankingUpdaterRepository;

class DBTest extends TestCase
{
    public function test()
    {
        $a = app(SqliteStatisticsRankingUpdaterRepository::class);
        
        debug($a->test());

        $this->assertTrue(true);
    }
}
