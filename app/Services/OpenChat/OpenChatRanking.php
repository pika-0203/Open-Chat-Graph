<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Models\Repositories\OpenChatListRepositoryInterface;

class OpenChatRanking
{
    private OpenChatListRepositoryInterface $openChat;

    function __construct(OpenChatListRepositoryInterface $openChat)
    {
        $this->openChat = $openChat;
    }

    /**
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'updated_at' => int, 'is_alive' => bool, 'review_count' => int, 'crying_rating_count' => int, 'laughing_rating_count' => int, 'angry_rating_count' => int, 'last_posted_at' => int]]`
     */
    function get(int $limit)
    {
        return $this->arrayMergeAlternate(
            $this->openChat->findLatestByLastPostedAt(0, $limit),
            $this->openChat->findOrderByLaughingRatingCount(0, $limit),
            $limit
        );
    }

    private function arrayMergeAlternate(array $arr1, array $arr2, int $limit): array
    {
        // 初期化
        $mergedArray = [];
        $count = 0;
        $offset = 0;

        // $arr1 の要素を先頭に追加
        foreach ($arr1 as $key => $value) {
            $mergedArray[] = $value;
            $count++;

            // $arr2 の要素を交互に追加
            for ($i = 0; $i < $limit; $i++) {
                $data2 = $arr2[$key + $offset] ?? null;
                if ($data2 === null) break;

                // $arr1 に既に存在する要素は追加しない
                if (in_array($data2, $arr1)) {
                    $offset++;
                    continue;
                }

                // 新しい要素を追加
                $mergedArray[] = $data2;
                $count++;

                // 制限に達したら終了
                if ($count >= $limit) break 2;
                break;
            }

            if ($count >= $limit) break;
        }

        return $mergedArray;
    }
}
