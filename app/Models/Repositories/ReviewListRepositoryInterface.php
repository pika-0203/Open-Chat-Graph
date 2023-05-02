<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface ReviewListRepositoryInterface
{
    /**
     * @return array `[['review_id' => int, 'title' => string, 'text' => string, 'rating' => int, 'time' => int, 'name' => string, 'img' => string]]`
     */
    public function findOrderByReviewIdDescByOpenChatId(int $open_chat_id, int $offset, int $limit): array;

    //public function findOrderByReviewIdDescByUserId(int $user_id, int $offset, int $limit): array;
}
