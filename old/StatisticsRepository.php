<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\Repositories\StatisticsRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatDto;
use Shadow\DB;

//class StatisticsRepository implements StatisticsRepositoryInterface
{
    public function addNewOpenChatStatisticsFromDto(OpenChatDto $dto): void
    {
        DB::execute(
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
            'INSERT INTO
                statistics (open_chat_id, member, date)
            SELECT
                :open_chat_id,
                :member,
                :date ON DUPLICATE KEY
            UPDATE
                member = :member';

        if (is_int($date)) {
            $date = date('Y-m-d', $date);
        }

        DB::execute($query, compact('open_chat_id', 'member', 'date'));
    }

    public function daleteDailyStatistics(int $open_chat_id): void
    {
        DB::execute(
            'DELETE FROM statistics WHERE open_chat_id = :open_chat_id',
            compact('open_chat_id')
        );
    }

    public function getMemberChangeWithinLastWeek(int $open_chat_id): bool
    {
        $query =
            'SELECT
                CASE
                    WHEN COUNT(DISTINCT member) > 1 THEN 1
                    WHEN MIN(`date`) > DATE_SUB(:date, INTERVAL 7 DAY) THEN 1
                    ELSE 0
                END AS member_change
            FROM
                statistics
            WHERE
                open_chat_id = :open_chat_id
                AND `date` BETWEEN DATE_SUB(:date, INTERVAL 7 DAY)
                AND :date';

        $date = date('Y-m-d');

        return DB::execute($query, compact('open_chat_id', 'date'))
            ->fetchColumn() !== 0;
    }

    public function mergeDuplicateOpenChatStatistics(int $duplicated_id, int $open_chat_id): void
    {
        $statistics = DB::fetchAll(
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
            DB::execute(
                'INSERT
                    IGNORE INTO statistics (open_chat_id, member, date)
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
            'SELECT
                open_chat_id
            FROM
                statistics
            WHERE
                `date` BETWEEN DATE_SUB(:curDate, INTERVAL 7 DAY)
                AND :curDate
            GROUP BY
                open_chat_id
            HAVING
                0 < (
                    CASE
                        WHEN COUNT(DISTINCT member) > 1 THEN 1
                        WHEN MIN(`date`) > DATE_SUB(:curDate, INTERVAL 7 DAY) THEN 1
                        ELSE 0
                    END
                )';

        return DB::execute($query, ['curDate' => date('Y-m-d', time())])
            ->fetchAll(\PDO::FETCH_COLUMN, 0);
    }
}
