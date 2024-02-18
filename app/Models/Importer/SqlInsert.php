<?php

declare(strict_types=1);

namespace App\Models\Importer;

class SqlInsert extends AbstractSqlImporter
{
    /**
     * @throws \RuntimeException
     * @throws \PDOException
     */
    function importWithKeys(\PDO $pdo, string $tableName, array $keys, array $data, int $chunkSize = 2000): int
    {
        if (empty($data)) {
            return 0;
        }

        $rowCount = 0;
        foreach (array_chunk($data, $chunkSize) as $chunk) {
            $rowCount += $this->importProsess($pdo, $keys, $chunk, $tableName);
        }

        return $rowCount;
    }

    protected function importProsess(\PDO $pdo, array $keys, array $chunk, string $tableName): int
    {
        return $this->execute($pdo, $keys, $chunk, $tableName);
    }

    protected function buildQuery(array $keys, array $chunk, string $tableName): string
    {
        $columns = implode(',', $keys);
        $columns = "({$columns})";

        $values = implode(',', array_map(fn ($row) => "(" . implode(",", array_map(fn ($value) => is_int($value) ? $value : "'{$value}'", $row)) . ")", $chunk));

        $query = "INSERT IGNORE INTO {$tableName} {$columns} VALUES {$values}";

        return $query;
    }
}
