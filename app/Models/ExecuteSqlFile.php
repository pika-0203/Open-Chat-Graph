<?php

declare(strict_types=1);

namespace App\Models;

use Shadow\DB;

class ExecuteSqlFile
{
    private string $sqlFile;

    /**
     * @return int returns the number of rows that were modified or deleted by the SQL statement.  
     *             If no rows were affected, returns 0.
     */
    public function execute(string $sqlFile): int
    {
        $this->sqlFile = $sqlFile;
        return DB::transaction($this->execQueries(...));
    }

    private function execQueries(): int
    {
        $sqlQueries = explode(';', file_get_contents($this->sqlFile));

        foreach ($sqlQueries as $query) {
            if (trim($query) === '') {
                continue;
            }

            $rowCount = DB::$pdo->exec($query);
        }

        return $rowCount;
    }
}
