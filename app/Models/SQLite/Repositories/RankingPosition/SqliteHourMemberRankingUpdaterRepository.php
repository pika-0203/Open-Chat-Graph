<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\RankingPosition;

use App\Models\Importer\SqlInsert;
use App\Models\Repositories\RankingPosition\HourMemberRankingUpdaterRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Models\SQLite\SQLiteRankingPositionHour;
use Shadow\DB;

class SqliteHourMemberRankingUpdaterRepository implements HourMemberRankingUpdaterRepositoryInterface
{
    public function __construct(
        private SqlInsert $inserter,
        private StatisticsRepositoryInterface $statisticsRepository
    ) {
    }

    public function updateHourRankingTable(\DateTime $dateTime): int
    {
        $data = $this->buildRankingData($dateTime);
        if (!$data) {
            return 0;
        }

        DB::execute("TRUNCATE TABLE statistics_ranking_hour");
        return $this->inserter->import(DB::connect(), 'statistics_ranking_hour', $data);
    }

    /**
     * @return array{ id: int, open_chat_id: int, diff_member: int, percent_increase: float }[]
     */
    public function buildRankingData(\DateTime $dateTime): array
    {
        $data = $this->getHourRanking($dateTime);
        $filters = $this->statisticsRepository->getMemberChangeWithinLastWeekCacheArray($dateTime->format('Y-m-d'));

        $filterdIdArray = array_filter($data, fn ($row) => in_array($row['open_chat_id'], $filters) || $row['diff_member'] > 0);

        $result = [];
        foreach ($filterdIdArray as $id => $row) {
            $result[] = ['id' => $id + 1] + $row;
        }

        return $result;
    }

    /**
     * @return array{ open_chat_id: int, diff_member: int, percent_increase: float }[]
     */
    public function getHourRanking(\DateTime $dateTime): array
    {
        $dateTime2 = new \DateTime($dateTime->format('Y-m-d H:i:s'));
        $dateTime2->modify('-1 hour');

        $timeString = $dateTime->format('Y-m-d H:i:s');
        $timeString2 = $dateTime2->format('Y-m-d H:i:s');

        $query =
            "SELECT
                t1.open_chat_id AS open_chat_id,
                t1.member - t2.member AS diff_member,
                (
                    (
                        CAST(t1.member AS REAL) - CAST(t2.member AS REAL)
                    ) * 100.0 / CAST(t2.member AS REAL)
                ) AS percent_increase
            FROM
                member as t1
                JOIN(
                    SELECT
                        *
                    FROM
                        member AS t3
                    WHERE
                        t3.time = '{$timeString2}'
                ) AS t2 ON t1.open_chat_id = t2.open_chat_id
            WHERE
                t1.time = '{$timeString}'
            ORDER BY
	            (
                    CAST(t1.member AS REAL) - CAST(t2.member AS REAL)
                ) + (
                    (
                        (
                            CAST(t1.member AS REAL) - CAST(t2.member AS REAL)
                        ) / CAST(t2.member AS REAL)
                    ) * 10
                ) DESC";

        return SQLiteRankingPositionHour::fetchAll($query);
    }
}
