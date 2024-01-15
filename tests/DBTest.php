<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Shadow\DB;
use App\Models\GCE\DBGce;
use App\Models\GCE\GceDbTableSynchronizer;

class DBTest extends TestCase
{
    public function test()
    {
        /**
         * @var GceDbTableSynchronizer $gce
         */
        $gce = app(GceDbTableSynchronizer::class);
        $result = $gce->syncAll();

        debug($result);

        $this->assertTrue(true);
    }
}
