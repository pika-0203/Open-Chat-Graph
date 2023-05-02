<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class HandleNameAndReviewRepository implements HandleNameAndReviewRepositoryInterface
{
    private HandleNameRepositoryInterface $handleName;
    private ReviewRepositoryInterface $review;

    public function __construct(
        HandleNameRepositoryInterface $handleName,
        ReviewRepositoryInterface $review
    ) {
        $this->handleName = $handleName;
        $this->review = $review;
    }

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
    ): array|false {
        $callback = function () use (
            $user_id,
            $open_chat_id,
            $name,
            $img,
            $title,
            $text,
            $crying_rating,
            $laughing_rating,
            $angry_rating,
        ) {
            $handle_name_id = $this->handleName->addHandleName(
                $user_id,
                $open_chat_id,
                $name,
                $img,
            );

            if ($handle_name_id === 0) {
                return false;
            }

            $review_id = $this->review->addReview(
                $open_chat_id,
                $handle_name_id,
                $title,
                $text,
                $crying_rating,
                $laughing_rating,
                $angry_rating,
            );

            return compact('handle_name_id', 'review_id');
        };

        return DB::transaction($callback);
    }
}
