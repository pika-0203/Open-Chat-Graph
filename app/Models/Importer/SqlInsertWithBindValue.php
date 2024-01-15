<?php

declare(strict_types=1);

namespace App\Models\Importer;

class SqlInsertWithBindValue extends AbstractSqlImporter
{
    protected function importProsess(\PDO $pdo, array $keys, array $chunk, string $tableName): int
    {
        return $this->executeWithBindValue($pdo, $keys, $chunk, $tableName);
    }

    protected function buildQuery(array $keys, array $chunk, string $tableName): string
    {
        $columns = implode(',', $keys);

        $prepare = implode(',', array_fill(0, count($keys), '?'));
        $preparedStatments = implode(',', array_fill(0, count($chunk), "({$prepare})"));

        return "INSERT IGNORE INTO {$tableName} ({$columns}) VALUES {$preparedStatments}";
    }
}
