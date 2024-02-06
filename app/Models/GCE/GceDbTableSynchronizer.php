<?php

declare(strict_types=1);

namespace App\Models\GCE;

use Shadow\DB;
use App\Models\GCE\DBGce as GceVmSql;
use App\Models\Importer\SqlInsertUpdateWithBindValue;
use App\Models\Importer\SqlInsert;

class GceDbTableSynchronizer
{
    function __construct(
        private SqlInsertUpdateWithBindValue $inserterWithBindValue,
        private SqlInsert $inserter
    ) {
    }

    function syncLatestOpenChat(): int
    {
        $lastUpdatedAt = GceVmSql::fetchColumn("SELECT MAX(updated_at) FROM open_chat");
        if ($lastUpdatedAt === false) {
            return 0;
        }

        $data = DB::fetchAll("SELECT * FROM open_chat WHERE updated_at >= :lastUpdatedAt", compact('lastUpdatedAt'));
        if (!$data) {
            return 0;
        }

        return $this->inserterWithBindValue->import(GceVmSql::connect(), 'open_chat', $data);
    }

    function syncOpenChatAll(): int
    {
        $data = DB::fetchAll("SELECT * FROM open_chat");
        if (!$data) {
            return 0;
        }

        return $this->inserterWithBindValue->import(GceVmSql::connect(), 'open_chat', $data);
    }
}
