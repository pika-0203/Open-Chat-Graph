<?php

declare(strict_types=1);

namespace App\Models\CommentRepositories;

use App\Config\CommentDBConfig;
use Shadow\DBInterface;
use Shadow\DB;

class CommentDB extends DB implements DBInterface
{
    public static ?\PDO $pdo = null;

    public static function connect(string $class = CommentDBConfig::class): \PDO
    {
        return parent::connect($class);
    }
}
