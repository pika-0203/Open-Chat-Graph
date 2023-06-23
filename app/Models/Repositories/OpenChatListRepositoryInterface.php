<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface OpenChatListRepositoryInterface
{
    /**
     * 削除されていないopen_chat_idを全て取得する
     * 
     * @return array `[['id' => int, 'updated_at' => Y-m-d]]`
     */
    public function getAliveOpenChatIdAll(): array;

    /**
     * メンバー数ランキングのレコード数を取得する
     */
    public function getMemberRankingRecordCount(): int;

    /**
     * 日次ランキングのレコード数を取得する
     */
    public function getDailyRankingRecordCount(): int;

    /**
     * 過去1週間ランキングのレコード数を取得する
     */
    public function getPastWeekRankingRecordCount(): int;

    /**
     * メンバー数ランキングを取得する
     * 
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int]]`
     */
    public function findMemberRanking(
        int $startId,
        int $endId,
    ): array;

    /**
     * 日次ランキングを取得する
     * 
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'diff_member' => int, percent_increase => float]]`
     */
    public function findMemberStatsDailyRanking(
        int $startId,
        int $endId,
    ): array;

    /**
     * 過去1週間ランキングを取得する
     * 
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'diff_member' => int, percent_increase => float]]`
     */
    public function findMemberStatsPastWeekRanking(
        int $startId,
        int $endId,
    ): array;

    /**
     * キーワードでタイトルと説明文から検索する
     * 
     * @return array `['count' => int, 'result' => [['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'diff_member' => int|null, percent_increase => float|null, 'count' => int]]]`
     */
    public function findByKeyword(string $keyword, int $offset, int $limit): array;
}
