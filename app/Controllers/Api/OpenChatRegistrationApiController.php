<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\OpenChat\Registration\OpenChatFromCrawlerRegistration;
use App\Models\GCE\GceDbRecordSynchronizer;

class OpenChatRegistrationApiController
{
    function register(OpenChatFromCrawlerRegistration $openChat, GceDbRecordSynchronizer $gce, string $url)
    {
        $openChat->getNumAddOpenChatPerMinute();
        $result = $openChat->registerOpenChatFromCrawler(sanitizeString($url));

        if ($result['message'] === 'オープンチャットを登録しました') {
            $gce->syncOpenChatById($result['id']);
        }

        return redirect()
            ->with($result);
    }
}
