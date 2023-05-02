<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class OpenChatListRepository implements OpenChatListRepositoryInterface
{
    public function findLatestByLastPostedAt(int $offset, int $limit): array
    {
        $query =
            'SELECT
                oc.*,
                UNIX_TIMESTAMP(oc.updated_at) AS updated_at,
                ocs.review_count,
                ocs.crying_rating_count,
                ocs.laughing_rating_count,
                ocs.angry_rating_count,
                UNIX_TIMESTAMP(ocs.last_posted_at) AS last_posted_at
            FROM
                open_chat_stats AS ocs
                JOIN open_chat AS oc ON ocs.open_chat_id = oc.id
            ORDER BY
                ocs.last_posted_at DESC,
                ocs.laughing_rating_count DESC
            LIMIT
                :offset, :limit';

        return DB::fetchAll($query, compact('offset', 'limit'));
    }

    public function findOrderByIdAsc(int $offset, int $limit): array
    {
        $query =
            'SELECT
                oc.*,
                UNIX_TIMESTAMP(oc.updated_at) AS updated_at,
                ocs.review_count,
                ocs.crying_rating_count,
                ocs.laughing_rating_count,
                ocs.angry_rating_count,
                UNIX_TIMESTAMP(ocs.last_posted_at) AS last_posted_at
            FROM
                open_chat_stats AS ocs
                JOIN open_chat AS oc ON ocs.open_chat_id = oc.id
            ORDER BY
                oc.id ASC
            LIMIT
                :offset, :limit';

        return DB::fetchAll($query, compact('offset', 'limit'));
    }

    public function findOrderByLaughingRatingCount(int $offset, int $limit): array
    {
        $query =
            'SELECT
                oc.*,
                UNIX_TIMESTAMP(oc.updated_at) AS updated_at,
                ocs.review_count,
                ocs.crying_rating_count,
                ocs.laughing_rating_count,
                ocs.angry_rating_count,
                UNIX_TIMESTAMP(ocs.last_posted_at) AS last_posted_at
            FROM
                open_chat_stats AS ocs
                JOIN open_chat AS oc ON ocs.open_chat_id = oc.id
            ORDER BY
                ocs.laughing_rating_count DESC
            LIMIT
                :offset, :limit';

        return DB::fetchAll($query, compact('offset', 'limit'));
    }

    public function findByKeyword(string $keyword, int $offset, int $limit): array
    {
        $query = fn (string $where): string =>
        "SELECT
            oc.*,
            UNIX_TIMESTAMP(oc.updated_at) AS updated_at,
            ocs.review_count,
            ocs.crying_rating_count,
            ocs.laughing_rating_count,
            ocs.angry_rating_count,
            UNIX_TIMESTAMP(ocs.last_posted_at) AS last_posted_at
        FROM
            open_chat_stats AS ocs
            JOIN open_chat AS oc ON ocs.open_chat_id = oc.id
        {$where}
        ORDER BY
            ocs.rating_count DESC
        LIMIT
            :offset, :limit";

        $whereClauseQuery = fn (int $i): string =>
        "(name LIKE :keyword{$i} OR description LIKE :keyword{$i})";

        return DB::executeLikeSearchQuery($query, $whereClauseQuery, $keyword, compact('offset', 'limit'));
    }
}
