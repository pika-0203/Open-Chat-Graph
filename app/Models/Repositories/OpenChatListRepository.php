<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class OpenChatListRepository implements OpenChatListRepositoryInterface
{
    private function getQuery(string $where, string $orderBy): string
    {
        return
            "SELECT
                oc.*,
                UNIX_TIMESTAMP(oc.updated_at) AS updated_at,
                ocs.review_count,
                ocs.crying_rating_count,
                ocs.laughing_rating_count,
                ocs.angry_rating_count,
                UNIX_TIMESTAMP(ocs.last_posted_at) AS last_posted_at
            FROM
                open_chat AS oc
                JOIN open_chat_stats AS ocs ON oc.id = ocs.open_chat_id
            {$where}
            {$orderBy}
            LIMIT
                :offset, :limit";
    }

    private function getMemberStatsRankingQuery(): string
    {
        return
            "SELECT
                oc.*,
                UNIX_TIMESTAMP(oc.updated_at) AS updated_at,
                ocs.review_count,
                ocs.crying_rating_count,
                ocs.laughing_rating_count,
                ocs.angry_rating_count,
                UNIX_TIMESTAMP(ocs.last_posted_at) AS last_posted_at,
                ranking.id AS ranking_id,
                ranking.diff_member,
                ranking.percent_increase
            FROM
                open_chat AS oc
                JOIN open_chat_stats AS ocs ON oc.id = ocs.open_chat_id
                JOIN (
                    SELECT
                        *
                    FROM
                        statistics_ranking
                    LIMIT
                        :offset, :limit
                ) AS ranking ON oc.id = ranking.open_chat_id
            ORDER BY
                ranking.id ASC";
    }

    public function findByKeyword(string $keyword, int $offset, int $limit): array
    {
        $query = function ($where) {
            return $this->getQuery(
                $where,
                'ORDER BY
                    ocs.rating_count DESC'
            );
        };

        $whereClauseQuery = fn ($i) => "(name LIKE :keyword{$i} OR description LIKE :keyword{$i})";

        return DB::executeLikeSearchQuery($query, $whereClauseQuery, $keyword, compact('offset', 'limit'));
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

    public function findMemberStatsRanking(int $offset, int $limit): array
    {
        $result = DB::fetchAll($this->getMemberStatsRankingQuery(), compact('offset', 'limit'));
        return $result;
    }

    public function getRankingRecordCount(): int
    {
        return (int)DB::execute('SELECT COUNT(id) FROM statistics_ranking')
            ->fetchColumn();
    }
}
