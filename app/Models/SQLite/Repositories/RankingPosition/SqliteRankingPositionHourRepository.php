<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;
use App\Models\SQLite\SQLiteInsertImporter;
use App\Models\SQLite\SQLiteRankingPositionHour;
use App\Services\OpenChat\Dto\OpenChatDto;

class SqliteRankingPositionHourRepository implements RankingPositionHourRepositoryInterface
{
    function __construct(
        private SQLiteInsertImporter $inserter
    ) {
    }

    private function insertFromDtoArray(string $tableName, string $fileTime, array $openChatDtoArray): int
    {
        $keys = [
            'emid',
            'member',
            'position',
            'category',
            'time'
        ];

        $data = [];
        foreach ($openChatDtoArray as $key => $dto) {
            /** @var OpenChatDto $dto */
            $data[] = [
                $dto->emid,
                $dto->memberCount,
                $key + 1,
                $dto->category ?? 0,
                $fileTime
            ];
        }

        return $this->inserter->importWithKeys(SQLiteRankingPositionHour::connect(), $tableName, $keys, $data, 500);
    }

    public function insertRankingHourFromDtoArray(string $fileTime, array $openChatDtoArray): int
    {
        return $this->insertFromDtoArray('ranking', $fileTime, $openChatDtoArray);
    }

    public function insertRisingHourFromDtoArray(string $fileTime, array $openChatDtoArray): int
    {
        return $this->insertFromDtoArray('rising', $fileTime, $openChatDtoArray);
    }

    private function getMinPositionHour(string $tableName, \DateTime $date, bool $all): array
    {
        $dateString = $date->format('Y-m-d');
        $isAll = $all ? '' : 'NOT';

        $query =
            "SELECT
                emid,
                category,
                MIN(position) as position,
                time
            FROM
                {$tableName}
            WHERE
                DATE(time) = '{$dateString}'
                AND {$isAll} category = 0
            GROUP BY
                emid";

        return SQLiteRankingPositionHour::fetchAll($query);
    }

    public function getMinRankingHour(\DateTime $date, bool $all = false): array
    {
        return $this->getMinPositionHour('ranking', $date, $all);
    }

    public function getMinRisingHour(\DateTime $date, bool $all = false): array
    {
        return $this->getMinPositionHour('rising', $date, $all);
    }

    public function getTotalCount(\DateTime $date): array
    {
        $dateString = $date->format('Y-m-d');

        $query =
            "SELECT
                ra.category AS category,
                ifnull(ri.count, 0) AS total_count_rising,
                ra.count AS total_count_ranking,
                ra.time AS time
            FROM
                (
                    SELECT
                        count(*) as count,
                        category,
                        time
                    FROM
                        ranking
                    WHERE
                        DATE(time) = '{$dateString}'
                    GROUP BY
                        time,
                        category
                ) AS ra
                LEFT JOIN (
                    SELECT
                        count(*) as count,
                        category,
                        time
                    FROM
                        rising
                    WHERE
                        DATE(time) = '{$dateString}'
                    GROUP BY
                        time,
                        category
                ) AS ri ON ra.category = ri.category
                AND ra.time = ri.time";

        return SQLiteRankingPositionHour::fetchAll($query);
    }

    public function dalete(\DateTime $date): void
    {
        $dateString = $date->format('Y-m-d');
        SQLiteRankingPositionHour::execute("DELETE FROM ranking WHERE DATE(time) <= '{$dateString}'");
        SQLiteRankingPositionHour::execute("DELETE FROM rising WHERE DATE(time) <= '{$dateString}'");
        SQLiteRankingPositionHour::$pdo->exec('VACUUM;');
    }
}
