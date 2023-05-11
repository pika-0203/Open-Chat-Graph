<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class OpenChatListRepository implements OpenChatListRepositoryInterface
{
    public function getRankingRecordCount(): int
    {
        return (int)DB::execute(
            'SELECT COUNT(id) FROM statistics_ranking'
        )->fetchColumn();
    }

    public function findMemberStatsRanking(int $startId, int $endId): array
    {
        $query =
            "SELECT
                oc.id,
                oc.name,
                oc.url,
                oc.img_url,
                oc.description,
                oc.member,
                -- ocs.review_count,
                -- ocs.crying_rating_count,
                -- ocs.laughing_rating_count,
                -- ocs.angry_rating_count,
                -- UNIX_TIMESTAMP(ocs.last_posted_at) AS last_posted_at,
                -- ranking.id AS ranking_id,
                ranking.diff_member,
                ranking.percent_increase
            FROM
                open_chat AS oc
                -- JOIN open_chat_stats AS ocs ON oc.id = ocs.open_chat_id
                JOIN (
                    SELECT
                        *
                    FROM
                        statistics_ranking
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
            -- ocs.review_count,
            -- ocs.crying_rating_count,
            -- ocs.laughing_rating_count,
            -- ocs.angry_rating_count,
            -- UNIX_TIMESTAMP(ocs.last_posted_at) AS last_posted_at,
            COALESCE(ranking.diff_member, 0) AS diff_member,
            COALESCE(ranking.percent_increase, 0) AS percent_increase
        FROM
            open_chat AS oc
            -- JOIN open_chat_stats AS ocs ON oc.id = ocs.open_chat_id
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

    public function findOrderByIdAsc(int $offset, int $limit): array
    {
        $orderBy =
            'ORDER BY
                oc.id ASC';

        return DB::fetchAll($this->getQuery('', $orderBy), compact('offset', 'limit'));
    }

    public function findLatestByLastPostedAt(int $offset, int $limit): array
    {
        $orderBy =
            'ORDER BY
                ocs.last_posted_at DESC,
                ocs.laughing_rating_count DESC';

        return DB::fetchAll($this->getQuery('', $orderBy), compact('offset', 'limit'));
    }

    public function findOrderByLaughingRatingCount(int $offset, int $limit): array
    {
        $orderBy =
            'ORDER BY
                ocs.laughing_rating_count DESC';

        return DB::fetchAll($this->getQuery('', $orderBy), compact('offset', 'limit'));
    }

    private function getQuery(string $where, string $orderBy): string
    {
        return
            "SELECT
                oc.id,
                oc.name,
                oc.url,
                oc.img_url,
                oc.description,
                oc.member,
                -- ocs.review_count,
                -- ocs.crying_rating_count,
                -- ocs.laughing_rating_count,
                -- ocs.angry_rating_count,
                -- UNIX_TIMESTAMP(ocs.last_posted_at) AS last_posted_at,
                COALESCE(ranking.diff_member, 0) AS diff_member,
                COALESCE(ranking.percent_increase, 0) AS percent_increase
            FROM
                open_chat AS oc
                -- JOIN open_chat_stats AS ocs ON oc.id = ocs.open_chat_id
                LEFT JOIN statistics_ranking AS ranking ON oc.id = ranking.open_chat_id
            {$where}
            {$orderBy}
            LIMIT
                :offset, :limit";
    }
}
