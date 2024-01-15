<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GCE\GceDbTableSynchronizer;
use App\Models\GCE\GceRankingUpdater;
use App\Models\GCE\DBGce;
use App\Models\Repositories\RepositoryCache;

class GceDifferenceUpdater
{
    private GceDbTableSynchronizer $tableSyncer;
    private GceRankingUpdater $ranking;

    function __construct(
        GceDbTableSynchronizer $tableSyncer,
        GceRankingUpdater $ranking,
    ) {
        $this->ranking = $ranking;
        $this->tableSyncer = $tableSyncer;
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
        $count = 0;
        foreach (RepositoryCache::$deleteOpenChat as $id) {
            $count++;
            DBGce::execute("DELETE FROM open_chat WHERE id = :id", compact('id'));
        }

        return $count;
    }
}
