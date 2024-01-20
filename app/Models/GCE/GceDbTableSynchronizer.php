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

    function syncOpenChatArchive(bool $all = false): int
    {
        if ($all) {
            $where = '';
        } else {
            $maxId = GceVmSql::fetchColumn("SELECT MAX(archive_id) FROM open_chat_archive");
            $where = $maxId ? " WHERE archive_id > {$maxId}" : '';
        }

        $data = DB::fetchAll("SELECT * FROM open_chat_archive" . $where);
        if (!$data) {
            return 0;
        }

        return $this->inserterWithBindValue->import(GceVmSql::connect(), 'open_chat_archive', $data);
    }

    function syncOpenChatMerged(): int
    {
        $data = DB::fetchAll("SELECT * FROM open_chat_merged");
        if (!$data) {
            return 0;
        }

        return $this->inserter->import(GceVmSql::connect(), 'open_chat_merged', $data);
    }

    function syncUserRegistrationOpenChat(): int
    {
        $data = DB::fetchAll("SELECT * FROM user_registration_open_chat");
        if (!$data) {
            return 0;
        }

        return $this->inserter->import(GceVmSql::connect(), 'user_registration_open_chat', $data);
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
