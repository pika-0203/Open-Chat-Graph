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

    public function getDailyMemberStats(\DateTime $todayLastTime): array
    {
        $time = $todayLastTime->format('Y-m-d H:i:s');

        $query =
            "SELECT
                open_chat_id,
                member,
                DATE(time) AS date
            FROM
                member
            WHERE
                time = '{$time}'";

        return SQLiteRankingPositionHour::fetchAll($query);
    }

    private function getMinPositionHour(string $tableName, \DateTime $date, bool $all): array
    {
        $dateString = $date->format('Y-m-d');
        $isAll = $all ? '' : 'NOT';

        $query =
            "SELECT
                t1.open_chat_id,
                t1.category,
                t1.position,
                t1.time
            FROM
                {$tableName} AS t1
            WHERE
                t1.time = (
                    SELECT
                        MAX(subq.time)
                    FROM
                        (
                            SELECT
                                *
                            FROM
                                {$tableName} AS t2
                            WHERE
                                t2.position = (
                                    SELECT
                                        MAX(t3.position)
                                    FROM
                                        {$tableName} AS t3
                                    WHERE
                                        t3.open_chat_id = t2.open_chat_id
                                        AND DATE(t3.time) = '{$dateString}'
                                        AND {$isAll} t3.category = 0
                                )
                                AND DATE(t2.time) = '{$dateString}'
                                AND {$isAll} t2.category = 0
                        ) AS subq
                    WHERE
                        subq.open_chat_id = t1.open_chat_id
                )";

        return SQLiteRankingPositionHour::fetchAll($query);
    }

    private function getLastPositionHour(string $tableName, \DateTime $date, bool $all): array
    {
        $dateString = $date->format('Y-m-d');
        $isAll = $all ? '' : 'NOT';

        $query =
            "SELECT
                t1.open_chat_id,
                t1.category,
                t1.position,
                t1.time
            FROM
                {$tableName} AS t1
            WHERE
                t1.time = (
                    SELECT
                        MAX(t2.time)
                    FROM
                        {$tableName} AS t2
                    WHERE
                        t2.open_chat_id = t1.open_chat_id
                        AND DATE(t2.time) = '{$dateString}'
                        AND {$isAll} t2.category = 0
                )";

        return SQLiteRankingPositionHour::fetchAll($query);
    }

    public function getDaliyRanking(\DateTime $date, bool $all = false): array
    {
        return $this->getLastPositionHour('ranking', $date, $all);
    }

    public function getDailyRising(\DateTime $date, bool $all = false): array
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

    public function dalete(\DateTime $dateTime): void
    {
        $timeStr = $dateTime->format('Y-m-d H:i:s');
        SQLiteRankingPositionHour::execute("DELETE FROM ranking WHERE time < '{$timeStr}'");
        SQLiteRankingPositionHour::execute("DELETE FROM rising WHERE time < '{$timeStr}'");
        SQLiteRankingPositionHour::execute("DELETE FROM total_count WHERE time < '{$timeStr}'");
        SQLiteRankingPositionHour::$pdo->exec('VACUUM;');
    }

    public function insertTotalCount(string $fileTime): int
    {
        $totalCount = $this->getTotalCount(new \DateTime($fileTime), false);
        return $this->inserter->import(SQLiteRankingPositionHour::connect(), 'total_count', $totalCount, 500);
    }
}
