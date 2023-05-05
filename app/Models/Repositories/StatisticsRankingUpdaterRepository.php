<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class StatisticsRankingUpdaterRepository implements StatisticsRankingUpdaterRepositoryInterface
{
    public function updateCreateRankingTable(): int
    {
        return DB::transaction($this->executeUpdateCreate(...));
    }

    private function executeUpdateCreate(): int
    {
        $sqlFile = __DIR__ . '/sql/StatisticsRankingUpdaterRepository_updateCreateRankingTable.sql';
        $sqlQueries = explode(';', file_get_contents($sqlFile));

        foreach ($sqlQueries as $query) {
            if (trim($query) === '') {
                continue;
            }

            $rowCount = DB::$pdo->exec($query);
        }

        return $rowCount;
    }
}
