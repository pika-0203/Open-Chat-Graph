<?php

declare(strict_types=1);

use App\Config\AppConfig;
use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourMemberDto;
use App\Models\SQLite\SQLiteRankingPositionHour;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use App\Services\StaticData\StaticTopPageDataGenerator;
use PHPUnit\Framework\TestCase;

class DBTest extends TestCase
{
    public function test()
    {
        debug(OpenChatServicesUtility::getCronModifiedStatsMemberDate());
        $this->assertTrue(true);
    }
}
