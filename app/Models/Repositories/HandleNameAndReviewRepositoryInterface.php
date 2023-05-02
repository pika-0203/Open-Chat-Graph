<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface HandleNameAndReviewRepositoryInterface
{
    /**
     * @return array `['handle_name_id' => int, 'review_id' => int]`
     */
    public function addHandleNameAndReview(
        int $user_id,
        int $open_chat_id,
        string $name,
        string $img,
        string $title,
        string $text,
        bool $crying_rating,
        bool $laughing_rating,
        bool $angry_rating,
    ): array|false;
}
