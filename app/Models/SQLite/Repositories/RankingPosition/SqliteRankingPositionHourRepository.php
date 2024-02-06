<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourInsertDto;
use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;
use App\Models\SQLite\SQLiteInsertImporter;
use App\Models\SQLite\SQLiteRankingPositionHour;

class SqliteRankingPositionHourRepository implements RankingPositionHourRepositoryInterface
{
    function __construct(
        private SQLiteInsertImporter $inserter
    ) {
    }

    /**
     * @param RankingPositionHourInsertDto[] $insertDtoArray
     */
    private function insertFromDtoArray(string $tableName, string $fileTime, array $insertDtoArray): int
    {
        $keys = [
            'open_chat_id',
            'position',
            'category',
            'time'
        ];

        $data = array_map(function (RankingPositionHourInsertDto $dto) use ($fileTime) {
            return [
                $dto->open_chat_id,
                $dto->position,
                $dto->category,
                $fileTime
            ];
        }, $insertDtoArray);

        return $this->inserter->importWithKeys(SQLiteRankingPositionHour::connect(), $tableName, $keys, $data, 500);
    }

    public function insertRankingHourFromDtoArray(string $fileTime, array $insertDtoArray): int
    {
        return $this->insertFromDtoArray('ranking', $fileTime, $insertDtoArray);
    }

    public function insertRisingHourFromDtoArray(string $fileTime, array $insertDtoArray): int
    {
        return $this->insertFromDtoArray('rising', $fileTime, $insertDtoArray);
    }

    /**
     * @param RankingPositionHourInsertDto[] $insertDtoArray
     */
    public function insertHourMemberFromDtoArray(string $fileTime, array $insertDtoArray): int
    {
        $keys = [
            'open_chat_id',
            'member',
            'time'
        ];

        $data = array_map(function (RankingPositionHourInsertDto $dto) use ($fileTime) {
            return [
                $dto->open_chat_id,
                $dto->member,
                $fileTime
            ];
        }, $insertDtoArray);

        return $this->inserter->importWithKeys(SQLiteRankingPositionHour::connect(), 'member', $keys, $data, 500);
    }

    private function getMinPositionHour(string $tableName, \DateTime $date, bool $all): array
    {
        $dateString = $date->format('Y-m-d');
        $isAll = $all ? '' : 'NOT';

        $query =
            "SELECT
                open_chat_id,
                category,
                MIN(position) as position,
                time
            FROM
                {$tableName}
            WHERE
                DATE(time) = '{$dateString}'
                AND {$isAll} category = 0
            GROUP BY
                open_chat_id";

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

    public function getTotalCount(\DateTime $date, bool $isDate = true): array
    {
        if ($isDate) {
            $dateString = $date->format('Y-m-d');
            $where = "DATE(time) = '{$dateString}'";
        } else {
            $timeString = $date->format('Y-m-d H:i:s');
            $where = "time = '{$timeString}'";
        }

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
                        {$where}
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
                        {$where}
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
        SQLiteRankingPositionHour::execute("DELETE FROM total_count WHERE DATE(time) <= '{$dateString}'");
        SQLiteRankingPositionHour::$pdo->exec('VACUUM;');
    }

    public function insertTotalCount(string $fileTime): int
    {
        $totalCount = $this->getTotalCount(new \DateTime($fileTime), false);
        return $this->inserter->import(SQLiteRankingPositionHour::connect(), 'total_count', $totalCount, 500);
    }
}
