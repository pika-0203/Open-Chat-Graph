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
        $time = $todayLastTime->format('Y-m-d');

        $query =
            "SELECT
                open_chat_id,
                member,
                DATE(time) AS date
            FROM
                member
            WHERE
                DATE(time) = '{$time}'
            GROUP BY
                open_chat_id
            HAVING
                MAX(time)";

        return SQLiteRankingPositionHour::fetchAll($query);
    }

    public function getHourlyMemberColumn(\DateTime $lastTime): array
    {
        $time = $lastTime->format('Y-m-d H:i:s');

        $query =
            "SELECT
                open_chat_id,
                member
            FROM
                member
            WHERE
                time = '{$time}'
            GROUP BY
                open_chat_id
            HAVING
                MAX(time)";

        return SQLiteRankingPositionHour::fetchAll($query);
    }

    private function getMinPosition(string $tableName, \DateTime $date, bool $all): array
    {
        $dateString = $date->format('Y-m-d');
        $isAll = $all ? '' : 'NOT';

        $query =
            "SELECT
                t1.open_chat_id AS open_chat_id,
                t1.category AS category,
                t1.position AS position,
                MAX(t1.time) AS time
            FROM
                (
                    SELECT
                        *
                    FROM
                        {$tableName} AS t2
                    WHERE
                        t2.position = (
                            SELECT
                                MIN(t3.position)
                            FROM
                                {$tableName} AS t3
                            WHERE
                                t3.open_chat_id = t2.open_chat_id
                                AND DATE(t3.time) = '{$dateString}'
                                AND {$isAll} t3.category = 0
                        )
                        AND DATE(t2.time) = '{$dateString}'
                        AND {$isAll} t2.category = 0
                ) AS t1
            GROUP BY
                open_chat_id";

        return SQLiteRankingPositionHour::fetchAll($query);
    }

    private function getIdArray(string $tableName, string $dateString, bool $all): array
    {
        $isAll = $all ? '' : 'NOT';

        $query =
            "SELECT 
                open_chat_id
            FROM 
                {$tableName}
            WHERE
                DATE(time) = '{$dateString}'
                AND {$isAll} category = 0
            GROUP BY
                open_chat_id";

        return SQLiteRankingPositionHour::fetchAll($query, null, [\PDO::FETCH_COLUMN, 0]);
    }

    private function getMedianPositionQuery(string $tableName, int $open_chat_id, string $dateString, bool $all): array|false
    {
        $isAll = $all ? '' : 'NOT';

        $query =
            "SELECT
                open_chat_id,
                category,
                position,
                time
            FROM
                {$tableName}
            WHERE
                DATE(time) = '{$dateString}'
                AND {$isAll} category = 0
                AND open_chat_id = '{$open_chat_id}'
            ORDER BY
                position ASC";

        /** @var array{ open_chat_id:int, category:int, position:int, time: string }[] $records */
        $records = SQLiteRankingPositionHour::fetchAll($query);

        $count = count($records);
        if (!$count) return false;
        if ($count === 1) return $records[0];

        $centerCount = $count / 2;

        if (is_float($centerCount)) {
            // 奇数の場合
            $median = $records[(int)ceil($centerCount) - 1];
            $median['position'] = (int)ceil(
                ($median['position'] + $records[(int)floor($centerCount) - 1]['position']) / 2
            );
        } else {
            $median = $records[$centerCount - 1];
        }

        return $median;
    }

    private function getMedianPosition(string $tableName, \DateTime $date, bool $all): array
    {
        $dateString = $date->format('Y-m-d');
        $idArray = $this->getIdArray($tableName, $dateString, $all);

        $result = [];
        foreach ($idArray as $id) {
            $record = $this->getMedianPositionQuery($tableName, $id, $dateString, $all);
            if (!$record) continue;
            $result[] = $record;
        }

        return $result;
    }

    public function getDaliyRanking(\DateTime $date, bool $all = false): array
    {
        return $this->getMedianPosition('ranking', $date, $all);
    }

    public function getDailyRising(\DateTime $date, bool $all = false): array
    {
        return $this->getMinPosition('rising', $date, $all);
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
        SQLiteRankingPositionHour::execute("DELETE FROM member WHERE time < '{$timeStr}'");
        SQLiteRankingPositionHour::$pdo->exec('VACUUM;');
    }

    public function insertTotalCount(string $fileTime): int
    {
        $totalCount = $this->getTotalCount(new \DateTime($fileTime), false);
        return $this->inserter->import(SQLiteRankingPositionHour::connect(), 'total_count', $totalCount, 500);
    }

    public function getLastHour(): string|false
    {
        return SQLiteRankingPositionHour::fetchColumn(
            "SELECT
                time
            FROM
                total_count
            GROUP BY
                time
            HAVING
                count(time) = 25
            ORDER BY
                time DESC
            LIMIT
                1"
        );
    }
}
