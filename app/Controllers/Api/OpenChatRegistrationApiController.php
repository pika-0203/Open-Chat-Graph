<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\OpenChat\Registration\OpenChatFromCrawlerRegistration;

class OpenChatRegistrationApiController
{
    function register(OpenChatFromCrawlerRegistration $openChat, string $url)
    {
        checkLineSiteRobots();
        
        $openChat->getNumAddOpenChatPerMinute();
        $result = $openChat->registerOpenChatFromCrawler(sanitizeString($url));

        return redirect('register')
            ->with($result);
    }
}
