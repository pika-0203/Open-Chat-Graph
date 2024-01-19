<?php

declare(strict_types=1);

namespace App\Models\SQLite;

use Shadow\DBInterface;

class SQLiteRankingPosition extends AbstractSQLite implements DBInterface
{
    public static string $dbfile = __DIR__ . '/../../../storage/SQLite/ranking_position/ranking_position.db';
    public static ?\PDO $pdo = null;
}