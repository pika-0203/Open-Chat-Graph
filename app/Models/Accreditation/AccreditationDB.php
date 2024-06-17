<?php

declare(strict_types=1);

namespace App\Models\Accreditation;

use App\Config\AccreditationDBConfig;
use Shadow\DBInterface;
use Shadow\DB;

class AccreditationDB extends DB implements DBInterface
{
    public static ?\PDO $pdo = null;

    public static function connect(string $class = AccreditationDBConfig::class): \PDO
    {
        return parent::connect($class);
    }
}
