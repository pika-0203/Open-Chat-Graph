<?php

declare(strict_types=1);

namespace App\Models\GCE;

use Shadow\DB;
use App\Models\GCE\DBGce as GceVmSql;
use App\Models\Importer\SqlInsertWithBindValue;
use App\Models\Importer\SqlInsert;

class GceDbRecordSynchronizer
{
    function __construct(
        private SqlInsertWithBindValue $inserterWithBindValue,
        private SqlInsert $inserter
    ) {
    }

    function syncOpenChatById(int $id): bool
    {
        $param = compact('id');

        $data = DB::fetch("SELECT * FROM open_chat WHERE id = :id", $param);
        if (!$data) {
            return false;
        }

        $this->inserterWithBindValue->import(GceVmSql::connect(), 'open_chat', [$data]);

        $data = DB::fetchAll("SELECT * FROM user_registration_open_chat WHERE id = :id", $param);
        $this->inserter->import(GceVmSql::connect(), 'user_registration_open_chat', $data);

        return true;
    }

    function deleteOpenChatById(int $id)
    {
        GceVmSql::execute("DELETE FROM open_chat WHERE id = :id", compact('id'));
    }
}
