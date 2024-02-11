<?php

declare(strict_types=1);

use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourMemberDto;
use App\Models\SQLite\SQLiteRankingPositionHour;
use PHPUnit\Framework\TestCase;

class getHourRankingTest extends TestCase
{
    public function test()
    {
        $query =
            "SELECT
                t1.open_chat_id,
                t1.member - t2.member AS diff_member,
                (
                    (
                        CAST(t1.member AS REAL) - CAST(t2.member AS REAL)
                    ) * 100.0 / CAST(t2.member AS REAL)
                ) AS percent_increase,
                (
                    CAST(t1.member AS REAL) - CAST(t2.member AS REAL)
                ) + (
                    (
                        (
                            CAST(t1.member AS REAL) - CAST(t2.member AS REAL)
                        ) / CAST(t2.member AS REAL)
                    ) * 20
                ) AS index1
            FROM
                member as t1
                JOIN(
                    SELECT
                        *
                    FROM
                        member AS t3
                    WHERE
                        t3.time = '2024-02-10 18:30:00'
                ) AS t2 ON t1.open_chat_id = t2.open_chat_id
            WHERE
                t1.time = '2024-02-10 17:30:00'";

        $result = SQLiteRankingPositionHour::fetchAll($query);

        $index1Array = array_column($result, 'index1');
        array_multisort($index1Array, SORT_DESC, $result);

        debug(array_slice($result, 0, 100));

        $this->assertTrue(true);
    }
}
