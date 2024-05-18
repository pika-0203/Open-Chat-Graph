<?php

declare(strict_types=1);

use App\Config\AdminConfig;
use App\Config\AppConfig;
use PHPUnit\Framework\TestCase;

class AdminToolTest extends TestCase
{
    public function test()
    {
        //debug(purgeCacheCloudFlare(AdminConfig::CloudFlareZoneID, AdminConfig::CloudFlareApiKey, ['https://openchat-review.me']));
        touch(AppConfig::HOURLY_CRON_UPDATED_AT_DATETIME);
        debug(filemtime(AppConfig::HOURLY_CRON_UPDATED_AT_DATETIME));


        $this->assertTrue(true);
    }
}
