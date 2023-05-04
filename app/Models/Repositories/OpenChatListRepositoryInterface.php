<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface OpenChatListRepositoryInterface
{
    /**
     * ID順にオープンチャットを取得する
     * 
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'updated_at' => int, 'is_alive' => bool, 'review_count' => int, 'crying_rating_count' => int, 'laughing_rating_count' => int, 'angry_rating_count' => int, 'last_posted_at' => int]]`
     */
    public function findOrderByIdAsc(int $offset, int $limit): array;

    /**
     * キーワードでタイトルと説明文から検索する
     * 
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'updated_at' => int, 'is_alive' => bool, 'review_count' => int, 'crying_rating_count' => int, 'laughing_rating_count' => int, 'angry_rating_count' => int, 'last_posted_at' => int]]`
     */
    public function findByKeyword(string $keyword, int $offset, int $limit): array;

    /**
     * 最終レビュー日時が新しい順にオープンチャットを取得する
     * 
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'updated_at' => int, 'is_alive' => bool, 'review_count' => int, 'crying_rating_count' => int, 'laughing_rating_count' => int, 'angry_rating_count' => int, 'last_posted_at' => int]]`
     */
    public function findLatestByLastPostedAt(int $offset, int $limit): array;

    /**
     * 評価の総数が高い順にオープンチャットを取得する
     * 
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'updated_at' => int, 'is_alive' => bool, 'review_count' => int, 'crying_rating_count' => int, 'laughing_rating_count' => int, 'angry_rating_count' => int, 'last_posted_at' => int]]`
     */
    public function findOrderByLaughingRatingCount(int $offset, int $limit): array;

    /**
     * メンバー数増加ランキングを取得する
     * 
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'updated_at' => int, 'is_alive' => bool, 'review_count' => int, 'crying_rating_count' => int, 'laughing_rating_count' => int, 'angry_rating_count' => int, 'last_posted_at' => int, 'ranking_id' => int, 'diff_member' => int, percent_increase => float]]`
     */
    public function findMemberStatsRanking(int $offset, int $limit): array;
}
