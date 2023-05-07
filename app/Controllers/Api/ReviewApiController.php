<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\Review\AddReview;

class ReviewApiController
{
    public function add(
        AddReview $review,
        int $open_chat_id,
        string $name,
        string $img,
        string $title,
        string $text,
        string $emotions_rating
    ) {
        $result = $review->add(
            $open_chat_id,
            $name,
            $img,
            $title,
            $text,
            $emotions_rating
        );

        return redirect()
            ->with($result);
    }
}
