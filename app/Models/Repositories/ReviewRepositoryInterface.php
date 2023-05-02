<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface ReviewRepositoryInterface
{
    /**
     * @return array `['open_chat_id' => int, 'review_id' => int, 'handle_name_id' => int, 'title' => string, 'text' => string, 'crying_rating' => bool, 'laughing_rating' => bool, 'angry_rating' => bool, 'time' => int, 'name' => string, 'img' => string]`
     */
    public function getReviewById(int $open_chat_id, int $review_id): array|false;

    /**
     * @return int review_id
     */
    public function addReview(
        int $open_chat_id,
        int $handle_name_id,
        string $title,
        string $text,
        bool $crying_rating,
        bool $laughing_rating,
        bool $angry_rating
    ): int;
}
