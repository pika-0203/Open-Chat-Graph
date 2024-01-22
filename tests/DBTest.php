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
        $result = DB::fetchAll('SELECT * FROM open_chat LIMIT 10', null, [PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC]);

        debug($result);

        $this->assertTrue(true);
    }
}
