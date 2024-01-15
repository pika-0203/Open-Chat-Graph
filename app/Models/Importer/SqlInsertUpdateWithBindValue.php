<?php

declare(strict_types=1);

namespace App\Models\Importer;

class SqlInsertUpdateWithBindValue extends AbstractSqlImporter
{
    protected function importProsess(\PDO $pdo, array $keys, array $chunk, string $tableName): int
    {
        return $this->executeWithBindValue($pdo, $keys, $chunk, $tableName);
    }
    
    protected function buildQuery(array $keys, array $chunk, string $tableName): string
    {
        $columns = implode(',', $keys);
        $columns = "({$columns})";

        $prepare = implode(',', array_fill(0, count($keys), '?'));
        $prepare = "({$prepare})";

        $paramsStatments = implode(',', array_fill(0, count($chunk), $prepare));

        $updateStatments = implode(',', array_map(fn ($colum) => "{$colum} = VALUES({$colum})", $keys));

        return "INSERT INTO {$tableName} {$columns} VALUES {$paramsStatments} ON DUPLICATE KEY UPDATE {$updateStatments}";
    }
}
