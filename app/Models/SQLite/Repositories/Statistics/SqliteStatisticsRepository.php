<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\Statistics;

use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Models\SQLite\SQLiteStatistics;
use App\Services\OpenChat\Dto\OpenChatDto;

class SqliteStatisticsRepository implements StatisticsRepositoryInterface
{
    public function addNewOpenChatStatisticsFromDto(OpenChatDto $dto): void
    {
        SQLiteStatistics::execute(
            "INSERT INTO
                statistics (open_chat_id, member, date)
            VALUES
                (:open_chat_id, :member, :date)",
            $dto->getStatisticsParams()
        );
    }

    public function insertUpdateDailyStatistics(int $open_chat_id, int $member, int|string $date): void
    {
        $query =
            'INSERT OR IGNORE INTO statistics (open_chat_id, member, date)
            VALUES
                (:open_chat_id, :member, :date)';

        if (is_int($date)) {
            $date = date('Y-m-d', $date);
        }

        SQLiteStatistics::execute($query, compact('open_chat_id', 'member', 'date'));
    }

    public function daleteDailyStatistics(int $open_chat_id): void
    {
        SQLiteStatistics::execute(
            'DELETE FROM statistics WHERE open_chat_id = :open_chat_id',
            compact('open_chat_id')
        );
    }

    public function getMemberChangeWithinLastWeek(int $open_chat_id): bool
    {
        $query =
            "SELECT
                CASE
                    WHEN COUNT(DISTINCT member) > 1 THEN 1
                    WHEN MIN(`date`) > datetime(:date, '-7 days') THEN 1
                    ELSE 0
                END AS member_change
            FROM
                statistics
            WHERE
                open_chat_id = :open_chat_id
                AND `date` BETWEEN datetime(:date, '-7 days')
                AND :date";

        $date = date('Y-m-d');

        return SQLiteStatistics::execute($query, compact('open_chat_id', 'date'))
            ->fetchColumn() !== 0;
    }

    public function mergeDuplicateOpenChatStatistics(int $duplicated_id, int $open_chat_id): void
    {
        $statistics = SQLiteStatistics::fetchAll(
            'SELECT 
                member,
                date
            FROM
                statistics
            WHERE 
                open_chat_id = :duplicated_id',
            compact('duplicated_id')
        );

        foreach ($statistics as $stat) {
            SQLiteStatistics::execute(
                'INSERT OR IGNORE INTO statistics (open_chat_id, member, date)
                VALUES
                    (
                        :open_chat_id,
                        :member,
                        :date
                    )',
                [
                    'open_chat_id' => $open_chat_id,
                    'member' => $stat['member'],
                    'date' => $stat['date'],
                ]
            );
        }
    }

    public function getMemberChangeWithinLastWeekCacheArray(): array
    {
        $query =
            "SELECT
                open_chat_id
            FROM
                statistics
            WHERE
                `date` BETWEEN datetime(:curDate, '-7 days')
                AND :curDate
            GROUP BY
                open_chat_id
            HAVING
                0 < (
                    CASE
                        WHEN COUNT(DISTINCT member) > 1 THEN 1
                        WHEN MIN(`date`) > datetime(:curDate, '-7 days') THEN 1
                        ELSE 0
                    END
                )";

        return SQLiteStatistics::execute($query, ['curDate' => date('Y-m-d', time())])
            ->fetchAll(\PDO::FETCH_COLUMN, 0);
    }
}
