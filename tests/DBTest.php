<?php

declare(strict_types=1);

use App\Config\AppConfig;
use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourMemberDto;
use App\Models\SQLite\SQLiteRankingPositionHour;
use App\Services\OpenChat\Enum\RankingType;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use App\Services\StaticData\StaticTopPageDataGenerator;
use PHPUnit\Framework\TestCase;
use Shadow\DB;

class DBTest extends TestCase
{
    public function test()
    {
        debug(AppConfig::ROOT_PATH . 'exec_parallel_downloader.php');

        $this->assertTrue(true);
    }
}
