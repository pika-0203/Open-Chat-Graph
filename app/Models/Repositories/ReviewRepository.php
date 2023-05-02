<?php
// TODO: いいねを取得するクエリを追加する
declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class ReviewRepository implements ReviewRepositoryInterface
{
    /**
     * @return array `['open_chat_id' => int, 'review_id' => int, 'handle_name_id' => int, 'title' => string, 'text' => string, 'crying_rating' => bool, 'laughing_rating' => bool, 'angry_rating' => bool, 'time' => int, 'name' => string, 'img' => string]`
     */
    public function getReviewById(int $open_chat_id, int $review_id): array|false
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
                AND reviews.review_id = :review_id';

        return DB::fetch($query, compact('open_chat_id', 'review_id'));
    }

    public function addReview(
        int $open_chat_id,
        int $handle_name_id,
        string $title,
        string $text,
        bool $crying_rating,
        bool $laughing_rating,
        bool $angry_rating
    ): int {
        $query =
            'INSERT INTO
                reviews (
                    open_chat_id,
                    handle_name_id,
                    title,
                    text,
                    crying_rating,
                    laughing_rating,
                    angry_rating
                )
            VALUES
                (
                    :open_chat_id,
                    :handle_name_id,
                    :title,
                    :text,
                    :crying_rating,
                    :laughing_rating,
                    :angry_rating
                )';

        return DB::executeAndGetLastInsertId(
            $query,
            compact('open_chat_id', 'handle_name_id', 'title', 'text', 'crying_rating', 'laughing_rating', 'angry_rating')
        );
    }
}
