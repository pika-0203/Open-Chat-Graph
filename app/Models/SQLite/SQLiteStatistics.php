<?php

declare(strict_types=1);

namespace App\Models\SQLite;

use Shadow\DBInterface;

class SQLiteStatistics extends AbstractSQLite implements DBInterface
{
    public static string $dbfile = __DIR__ . '/../../../storage/SQLite/statistics/statistics.db';
    public static ?\PDO $pdo = null;
}
