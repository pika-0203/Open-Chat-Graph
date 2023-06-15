<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class OpenChatListRepository implements OpenChatListRepositoryInterface
{
    public function getAliveOpenChatIdAll(): array
    {
        return DB::fetchAll(
            'SELECT id, DATE(updated_at) AS updated_at FROM open_chat WHERE is_alive = 1'
        );
    }

    public function getDailyRankingRecordCount(): int
    {
        return (int)DB::execute(
            'SELECT COUNT(id) FROM statistics_ranking'
        )->fetchColumn();
    }

    public function getPastWeekRankingRecordCount(): int
    {
        return (int)DB::execute(
            'SELECT COUNT(id) FROM statistics_ranking2'
        )->fetchColumn();
    }

    public function findMemberStatsDailyRanking(
        int $startId,
        int $endId,
    ): array {
        return $this->findMemberStatsRanking($startId, $endId, 'statistics_ranking');
    }

    public function findMemberStatsPastWeekRanking(
        int $startId,
        int $endId,
    ): array {
        return $this->findMemberStatsRanking($startId, $endId, 'statistics_ranking2');
    }

    private function findMemberStatsRanking(
        int $startId,
        int $endId,
        string $tableName
    ): array {
        $query =
            "SELECT
                oc.id,
                oc.name,
                oc.url,
                oc.img_url,
                oc.description,
                oc.member,
                ranking.diff_member,
                ranking.percent_increase
            FROM
                open_chat AS oc
                JOIN (
                    SELECT
                        *
                    FROM
                        {$tableName}
                    WHERE
                        id > :startId
                        AND id <= :endId
                ) AS ranking ON oc.id = ranking.open_chat_id
            ORDER BY
                ranking.id ASC";

        return DB::fetchAll($query, compact('startId', 'endId'));
    }

    public function findByKeyword(string $keyword, int $offset, int $limit): array
    {
        $query = fn ($where) =>
        "SELECT SQL_CALC_FOUND_ROWS
            oc.id,
            oc.name,
            oc.url,
            oc.img_url,
            oc.description,
            oc.member,
            ranking.diff_member AS diff_member,
            ranking.percent_increase AS percent_increase
        FROM
            open_chat AS oc
            LEFT JOIN statistics_ranking AS ranking ON oc.id = ranking.open_chat_id
        {$where}
        ORDER BY
            CASE
                WHEN oc.name LIKE :keyword0 AND ranking.id IS NOT NULL THEN 0
                WHEN oc.name LIKE :keyword0 AND ranking.id IS NULL THEN 1
                WHEN oc.description LIKE :keyword0 AND ranking.id IS NOT NULL THEN 2
                ELSE 3
            END,
            CASE
                WHEN oc.name LIKE :keyword0 AND ranking.id IS NULL THEN -oc.id
                ELSE ranking.id
            END ASC,
            CASE
                WHEN (oc.name LIKE :keyword0 OR oc.description LIKE :keyword0) AND ranking.id IS NULL THEN oc.id
                ELSE NULL
            END DESC
        LIMIT
            :offset, :limit";

        $whereClauseQuery = fn ($i) => "(oc.name LIKE :keyword{$i} OR oc.description LIKE :keyword{$i})";

        return [
            'result' => DB::executeLikeSearchQuery($query, $whereClauseQuery, $keyword, compact('offset', 'limit')),
            'count' => (int)DB::execute('SELECT FOUND_ROWS()')->fetchColumn()
        ];
    }
}
