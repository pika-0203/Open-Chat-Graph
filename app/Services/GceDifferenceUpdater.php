<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GCE\GceDbTableSynchronizer;
use App\Models\GCE\GceRankingUpdater;
use App\Models\GCE\GceDbRecordSynchronizer;
use App\Models\Repositories\RepositoryCache;

class GceDifferenceUpdater
{
    private GceDbTableSynchronizer $tableSyncer;
    private GceRankingUpdater $ranking;
    private GceDbRecordSynchronizer $gceDbRecordSynchronizer;

    function __construct(
        GceDbTableSynchronizer $tableSyncer,
        GceRankingUpdater $ranking,
        GceDbRecordSynchronizer $gceDbRecordSynchronizer,
    ) {
        $this->ranking = $ranking;
        $this->tableSyncer = $tableSyncer;
        $this->gceDbRecordSynchronizer = $gceDbRecordSynchronizer;
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
        $deleteOpenChat = RepositoryCache::$deleteOpenChat;
        array_map($this->gceDbRecordSynchronizer->deleteOpenChatById(...), $deleteOpenChat);
        return count($deleteOpenChat);
    }
}
