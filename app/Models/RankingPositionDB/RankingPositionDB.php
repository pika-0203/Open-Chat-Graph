<?php

declare(strict_types=1);

namespace App\Models\RankingPositionDB;

use App\Config\AppConfig;
use Shadow\DBInterface;
use Shadow\DB;
use Shared\MimimalCmsConfig;

class RankingPositionDB extends DB implements DBInterface
{
    public static ?\PDO $pdo = null;

    public static function connect(?array $config = null): \PDO
    {
        return parent::connect($config ?? [
            'dbName' => AppConfig::RANKING_POSITION_DB_NAME[MimimalCmsConfig::$urlRoot]
        ]);
    }
}
