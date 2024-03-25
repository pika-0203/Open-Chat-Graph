<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater;

use App\Models\Repositories\Log\LogRepositoryInterface;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Crawler\OpenChatApiFromEmidDownloader;
use Shadow\DB;

class InvitationTicketUpdater
{
    function __construct(
        private OpenChatApiFromEmidDownloader $openChatApiFromEmidDownloader,
        private UpdateOpenChatRepositoryInterface $updateOpenChatRepository,
        private LogRepositoryInterface $logRepository,
    ) {
    }

    function updateInvitationTicket(int $open_chat_id, string $emid): bool
    {
        try {
            $dto = $this->openChatApiFromEmidDownloader->fetchOpenChatDto($emid);
            if(!$dto) return false;
        } catch(\RuntimeException $e) {
            // 再接続
            DB::$pdo = null;
            $this->logRepository->logUpdateOpenChatError($open_chat_id, $e->getMessage());
            return false;
        }

        $this->updateOpenChatRepository->updateUrl($open_chat_id, $dto->invitationTicket);
        return true;
    }
}
