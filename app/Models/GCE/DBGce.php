<?php

declare(strict_types=1);

namespace App\Models\GCE;

use Shadow\DBInterface;
use Shadow\DB;

class DBGce extends DB implements DBInterface
{
    public static ?\PDO $pdo = null;

    /**
     * @throws \PDOException
     */
    public static function connect(string $configClass = \App\Config\Shadow\GceDatabaseConfig::class): \PDO
    {
        if (static::$pdo !== null) {
            return static::$pdo;
        }

        static::$pdo = new \PDO(
            'mysql:host=' . $configClass::HOST . ';dbname=' . $configClass::DB_NAME . ';charset=utf8mb4',
            $configClass::USER_NAME,
            $configClass::PASSWORD,
            [
                \PDO::ATTR_PERSISTENT => $configClass::ATTR_PERSISTENT,
                \PDO::MYSQL_ATTR_COMPRESS => true,
            ]
        );

        static::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return static::$pdo;
    }
}
