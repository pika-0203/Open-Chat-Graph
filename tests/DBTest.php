<?php

declare(strict_types=1);

use App\Config\AppConfig;
use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourMemberDto;
use App\Models\SQLite\SQLiteRankingPositionHour;
use PHPUnit\Framework\TestCase;

class DBTest extends TestCase
{
    public function test()
    {
        $data = serialize(
            [
                'rankingUpdatedAt' => time(),
            ]
        );

        safeFileRewrite(AppConfig::TOP_RANKING_HOUR_INFO_FILE_PATH, $data);

        $this->assertTrue(true);
    }
}
