<?php

declare(strict_types=1);

namespace App\Models\SQLite;

use Shadow\DBInterface;

class SQLiteRankingPosition extends AbstractSQLite implements DBInterface
{
    public static ?\PDO $pdo = null;

    /**
     * @param ?array $config array{mode?: ?string} $config mode default is '?mode=rwc'
     */
    public static function connect(?array $config = null): \PDO
    {
        return parent::connect([
            'storageFileKey' => 'sqliteRankingPositionDb',
            'mode' => $config['mode'] ?? null
        ]);
    }
}
