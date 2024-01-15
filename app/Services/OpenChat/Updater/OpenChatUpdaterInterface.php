<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater;

use App\Services\OpenChat\Dto\OpenChatDto;

interface OpenChatUpdaterInterface
{
    function updateOpenChat(int $open_chat_id, OpenChatDto $ocDto): void;
}
