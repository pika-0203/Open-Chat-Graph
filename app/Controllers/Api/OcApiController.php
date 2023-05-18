<?php

declare(strict_types=1);

namespace App\Controllers\Api;

class OcApiController
{
    public function index(
        \App\Services\OpenChat\AddOpenChat $openChat,
        string $url
    ) {
        $result = $openChat->add(sanitizeString(removeZWS($url)));

        return redirect()
            ->with($result);
    }
}
