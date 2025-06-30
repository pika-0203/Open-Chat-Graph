<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Models\SQLite\SQLiteRankingPosition;

class SQLiteRankingPositionTest extends TestCase
{
    public function test()
    {
        /**
         * @var SQLiteRankingPosition $db
         */
        $db = app(SQLiteRankingPosition::class);

        $result = $db->fetchAll('SELECT * FROM ranking WHERE open_chat_id = 3 AND date = "2024-03-14"');

        debug($result);

        $this->assertTrue(true);
    }
}
