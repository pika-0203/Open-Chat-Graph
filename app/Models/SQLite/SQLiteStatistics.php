<?php

declare(strict_types=1);

namespace App\Models\SQLite;

use Shadow\DBInterface;
use Shadow\DB;

class SQLiteStatistics extends DB implements DBInterface
{
    public static string $dbfile = __DIR__ . '/../../../storage/SQLite/statistics/statistics.db';
    public static ?\PDO $pdo = null;

    /**
     * @throws \PDOException
     */
    public static function connect(string $dbfile = ''): \PDO
    {
        if (static::$pdo !== null) {
            return static::$pdo;
        }

        static::$pdo = new \PDO('sqlite:file:' . static::$dbfile . '?mode=rwc');

        return static::$pdo;
    }
}
