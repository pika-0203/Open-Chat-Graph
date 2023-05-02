<?php

declare(strict_types=1);

namespace App\Services\Review;

use App\Models\Repositories\HandleNameAndReviewRepositoryInterface;
use App\Models\Repositories\LogRepositoryInterface;
use App\Services\Auth;

class AddReview
{
    private HandleNameAndReviewRepositoryInterface $reviewRepository;
    private LogRepositoryInterface $logRepository;

    function __construct(
        HandleNameAndReviewRepositoryInterface $reviewRepository,
        LogRepositoryInterface $logRepository,
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->logRepository = $logRepository;
    }

    /**
     * DBにレビューを追加する
     * 
     * @param string $emotions_rating `crying` `laughing` `angry`
     * 
     * @return array `['message' => string, 'id' => array|null ['handle_name_id' => int, 'review_id' => int]]`
     * 
     * @throws \InvalidArgumentException `$emotions_rating`が`'crying'` `'laughing'` `'angry'`ではない場合
     */
    function add(
        int $open_chat_id,
        string $name,
        string $img,
        string $title,
        string $text,
        string $emotions_rating
    ): array|false {
        switch ($emotions_rating) {
            case 'crying':
                $crying_rating = true;
                break;
            case 'laughing':
                $laughing_rating = true;
                break;
            case 'angry':
                $angry_rating = true;
                break;
            default:
                throw new \InvalidArgumentException("Invalid emotions rating value: {$emotions_rating}");
        }

        $id = $this->reviewRepository->addHandleNameAndReview(
            Auth::id(),
            $open_chat_id,
            $name,
            $img,
            $title,
            $text,
            $crying_rating ?? false,
            $laughing_rating ?? false,
            $angry_rating ?? false
        );

        if ($id === false) {
            $this->logRepository->logAddReviewError(Auth::id(), getIP(), getUA(), "Duplication error: {$open_chat_id}");
            return $this->failMessage();
        } else {
            $this->logRepository->logAddReview(Auth::id(), $open_chat_id, getIP(), getUA());
            return $this->successMessage($id);
        }
    }

    private function failMessage(): array
    {
        return [
            'message' => 'オープンチャットを登録できませんでした。',
            'id' => null
        ];
    }

    private function successMessage(array $id): array
    {
        return [
            'message' => 'オープンチャットを登録しました。',
            'id' => $id
        ];
    }
}
