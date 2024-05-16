<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class CacheControl
{
    public function handle()
    {
        if (session_status() === 2)
            return;

        $cacheExpires = OpenChatServicesUtility::getModifiedCronTime('now');

        $cacheExpires->modify('+70minutes');
        
        setCacheHeaders($cacheExpires);
    }
}
