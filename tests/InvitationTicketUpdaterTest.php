<?php

declare(strict_types=1);

use App\Services\OpenChat\Updater\InvitationTicketUpdater;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use PHPUnit\Framework\TestCase;

class InvitationTicketUpdaterTest extends TestCase
{
    private InvitationTicketUpdater $inst;

    public function test()
    {
        $this->inst = app(InvitationTicketUpdater::class);
        $res = $this->inst->updateInvitationTicket(164927, 'gsIbu5PEPsmS7D0OKm_iwkQravvNFrahZuZyh-SzJ0tiFg-D6xT75wkDm9I');

        $this->assertTrue($res);
    }
}
