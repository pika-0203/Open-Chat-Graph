<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GCE\GceDbTableSynchronizer;
use App\Models\GCE\GceRankingUpdater;
use App\Models\GCE\GceDbRecordSynchronizer;
use App\Models\Repositories\DeleteOpenChatRepository;

class GceDifferenceUpdater
{
    function __construct(
        private GceDbTableSynchronizer $tableSyncer,
        private GceRankingUpdater $ranking,
        private GceDbRecordSynchronizer $gceDbRecordSynchronizer,
    ) {
    }

    function finalizeSyncLatest()
    {
        $this->tableSyncer->syncLatestOpenChat();
        $this->deleteOpenChatByRepositryCache();

        $this->tableSyncer->syncOpenChatArchive();
    }

    function finalizeOpenChatMerged()
    {
        $this->deleteOpenChatByRepositryCache();
        $this->tableSyncer->syncOpenChatMerged();
        $this->tableSyncer->syncUserRegistrationOpenChat();
    }

    function gceUpdateRanking()
    {
        $this->ranking->updateRanking();
    }

    private function deleteOpenChatByRepositryCache(): int
    {
        $deleteOpenChat = DeleteOpenChatRepository::getDeletedOpenChat();
        array_map($this->gceDbRecordSynchronizer->deleteOpenChatById(...), $deleteOpenChat);
        return count($deleteOpenChat);
    }
}
