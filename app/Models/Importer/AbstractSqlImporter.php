<?php

declare(strict_types=1);

namespace App\Models\Importer;

abstract class AbstractSqlImporter
{
    /**
     * @throws \RuntimeException
     * @throws \PDOException
     */
    function import(\PDO $pdo, string $tableName, array $data, int $chunkSize = 2000): int
    {
        if (empty($data)) {
            return 0;
        }

        $keys = $this->getColumKeys($data);
        if (!$keys) {
            throw new \RuntimeException('invalid array');
        }

        $rowCount = 0;
        foreach (array_chunk($data, $chunkSize) as $chunk) {
            $rowCount += $this->importProsess($pdo, $keys, $chunk, $tableName);
        }

        return $rowCount;
    }

    protected function getColumKeys(array $array): array|false
    {
        if (isset($array[0]) && !empty($array[0]) && !array_is_list($array[0])) {
            return array_keys($array[0]);
        }

        return false;
    }

    abstract protected function importProsess(\PDO $pdo, array $keys, array $chunk, string $tableName): int;

    protected function execute(\PDO $pdo, array $keys, array $chunk, string $tableName): int
    {
        return (int)$pdo->exec($this->buildQuery($keys, $chunk, $tableName));
    }

    protected function executeWithBindValue(\PDO $pdo, array $keys, array $chunk, string $tableName): int
    {
        $query = $this->buildQuery($keys, $chunk, $tableName);

        $params = [];
        foreach ($chunk as $row) {
            array_push($params, ...array_values($row));
        }

        $stmt = $pdo->prepare($query);
        $this->bindValue($stmt, $params);
        $stmt->execute();

        return $stmt->rowCount();
    }

    abstract protected function buildQuery(array $keys, array $chunk, string $tableName): string;

    protected function bindValue(\PDOStatement $stmt, array $params)
    {
        foreach ($params as $key => $value) {
            $key++;

            if ($value === null) {
                $stmt->bindValue($key, $value, \PDO::PARAM_NULL);
            } elseif (is_bool($value)) {
                $stmt->bindValue($key, $value, \PDO::PARAM_BOOL);
            } elseif (is_numeric($value)) {
                $type = is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $type);
            } else {
                $stmt->bindValue($key, $value, \PDO::PARAM_STR);
            }
        }
    }
}
