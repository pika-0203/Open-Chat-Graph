<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Updater\InvitationTicketUpdater;

class OpenChatHourlyInvitationTicketUpdater
{
    function __construct(
        private UpdateOpenChatRepositoryInterface $updateOpenChatRepository,
        private InvitationTicketUpdater $invitationTicketUpdater,
    ) {
    }

    function updateInvitationTicketAll()
    {
        $ocArray = $this->updateOpenChatRepository->getEmptyUrlOpenChatId();
        foreach ($ocArray as $oc) {
            $this->invitationTicketUpdater->updateInvitationTicket($oc['id'], $oc['emid']);
        }
    }
}
