<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\OpenChat\Registration\OpenChatFromCrawlerRegistration;
use Shared\Exceptions\BadRequestException;

class OpenChatRegistrationApiController
{
    function register(OpenChatFromCrawlerRegistration $openChat, string $url)
    {
        checkLineSiteRobots();

        $openChat->getNumAddOpenChatPerMinute();
        $result = $openChat->registerOpenChatFromCrawler(sanitizeString($url));

        if (is_int($result['id'])) {
            return redirect('oc/' . $result['id']);
        } else {
            throw new BadRequestException($result['message']);
        }
    }
}
