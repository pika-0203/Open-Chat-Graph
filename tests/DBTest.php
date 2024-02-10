<?php

declare(strict_types=1);

use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourMemberDto;
use App\Models\SQLite\SQLiteRankingPositionHour;
use PHPUnit\Framework\TestCase;

class DBTest extends TestCase
{
    public function test()
    {
        /**
         * @var RankingPositionHourMemberDto[] $anHourAgo
         */
        $anHourAgo = SQLiteRankingPositionHour::fetchAll(
            "SELECT open_chat_id, member FROM member WHERE time = '2024-02-10 17:30:00'",
            args: [\PDO::FETCH_CLASS, RankingPositionHourMemberDto::class]
        );

        /**
         * @var RankingPositionHourMemberDto[] $now
         */
        $now = SQLiteRankingPositionHour::fetchAll(
            "SELECT open_chat_id, member FROM member WHERE time = '2024-02-10 18:30:00'",
            args: [\PDO::FETCH_CLASS, RankingPositionHourMemberDto::class]
        );

        

        $this->assertTrue(true);
    }
}
