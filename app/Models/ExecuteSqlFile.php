<?php

declare(strict_types=1);

namespace App\Models;

use Shadow\DB;
use Shadow\DBInterface;

class ExecuteSqlFile
{
    /**
     * @return int returns the number of rows that were modified or deleted by the SQL statement.  
     *             If no rows were affected, returns 0.
     */
    public function execute(string $sqlFile): int
    {
        return DB::transaction(fn () => $this->execQueries($sqlFile, new DB));
    }

    public function execQueries(string $sqlFile, DBInterface $db): int
    {
        $sqlQueries = explode(';', file_get_contents($sqlFile));

        foreach ($sqlQueries as $query) {
            if (trim($query) === '') {
                continue;
            }

            $rowCount = $db->connect()->exec($query);
        }

        return $rowCount;
    }
}
