<?php
// TODO: いいねを取得するクエリを追加する
declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class ReviewListRepository implements ReviewListRepositoryInterface
{
    /**
     * @return array `[['review_id' => int, 'title' => string, 'text' => string, 'rating' => int, 'time' => int, 'name' => string, 'img' => string]]`
     */
    public function findOrderByReviewIdDescByOpenChatId(int $open_chat_id, int $offset, int $limit): array
    {
        $query =
            'SELECT
                reviews.*,
                UNIX_TIMESTAMP(reviews.time) AS time,
                handle_names.name,
                handle_names.img
            FROM
                reviews
                INNER JOIN handle_names ON reviews.handle_name_id = handle_names.id
            WHERE
                reviews.open_chat_id = :open_chat_id
            ORDER BY
                reviews.review_id DESC
            LIMIT
                :limit OFFSET :offset';

        return DB::fetchAll($query, compact('open_chat_id', 'offset', 'limit'));
    }

    //public function findOrderByReviewIdDescByUserId(int $user_id, int $offset, int $limit): array
}
