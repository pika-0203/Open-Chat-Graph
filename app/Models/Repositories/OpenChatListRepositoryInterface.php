<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface OpenChatListRepositoryInterface
{
    /**
     * 削除されていないopen_chat_idを全て取得する
     * 
     * @return array `[['id' => int, 'updated_at' => string]]`
     */
    public function getAliveOpenChatIdAll(): array;

    /**
     * 削除されていないすべてのレコード数を取得する
     */
    public function getRecordCount(): int;

    /**
     * 最近更新のあったレコード数を取得する
     */
    public function getRecentArchiveRecordCount(): int;

    /**
     * 日次ランキングのレコード数を取得する
     */
    public function getDailyRankingRecordCount(): int;

    /**
     * 過去1週間ランキングのレコード数を取得する
     */
    public function getPastWeekRankingRecordCount(): int;

    /**
     * 登録日順で取得する
     * 
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'datetime' => string]]`
     */
    public function findAllOrderByIdDesc(
        int $startId,
        int $endId,
    ): array;

    /**
     * 更新のあったオープンチャットを取得する
     * 
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'date' => string, 'archive_id' => int, 'update_description' => int, 'update_img' => int, 'update_name' => int]]`
     */
    public function findRecentArchive(
        int $startId,
        int $endId,
    ): array;

    /**
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'diff_member' => int, 'percent_increase' => float, 'ranking_id' => int]]`
     */
    public function getRankingRecordByMylist(array $idArray): array;

    /**
     * 更新履歴を取得する
     * 
     * @return array `[['id' => int, 'archive_id' => int, 'group_id' => int, 'name' => string, 'description' => string, 'img_url' => string, 'member' => int, 'archived_at' => int, 'update_description' => int, 'update_img' => int, 'update_name' => int]]`
     */
    public function findArchives(int $id): array;

    /**
     * 日次ランキングを取得する
     * 
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'diff_member' => int, 'percent_increase' => float]]`
     */
    public function findMemberStatsDailyRanking(
        int $startId,
        int $endId,
    ): array;

    /**
     * 過去1週間ランキングを取得する
     * 
     * @return array `[['id' => int, 'name' => string, 'url' => string, 'description' => string, 'img_url' => string, 'member' => int, 'diff_member' => int, 'percent_increase' => float]]`
     */
    public function findMemberStatsPastWeekRanking(
        int $startId,
        int $endId,
    ): array;
}
