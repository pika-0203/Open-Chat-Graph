<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Config\AppConfig;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class CacheControl
{
    public function handle()
    {
        if (session_status() === 2)
            return;

        $cacheExpires = OpenChatServicesUtility::getModifiedCronTime(
            file_get_contents(AppConfig::HOURLY_CRON_UPDATED_AT_DATETIME)
        );

        $cacheExpires->modify('+63minutes');

        setCacheHeaders($cacheExpires);
    }

    public function dailyCronCache()
    {
        $cacheExpires = new \DateTime(file_get_contents(AppConfig::DAILY_CRON_UPDATED_AT_DATE));

        $cacheExpires->modify('+2day');
        $cacheExpires->modify('+30minutes');

        setCacheHeaders($cacheExpires);
    }
}
