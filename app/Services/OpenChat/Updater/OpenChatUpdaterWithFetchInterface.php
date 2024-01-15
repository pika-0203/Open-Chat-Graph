<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater;

interface OpenChatUpdaterWithFetchInterface
{
    function fetchUpdateOpenChat(array $openChat): bool;
}
