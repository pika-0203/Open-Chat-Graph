<?php

declare(strict_types=1);

namespace App\Models\UserLogRepositories;

use App\Config\UserLogDBConfig;
use Shadow\DBInterface;
use Shadow\DB;

class UserLogDB extends DB implements DBInterface
{
    public static ?\PDO $pdo = null;

    public static function connect(string $class = UserLogDBConfig::class): \PDO
    {
        return parent::connect($class);
    }
}
