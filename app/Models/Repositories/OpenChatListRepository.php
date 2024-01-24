<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class OpenChatListRepository implements OpenChatListRepositoryInterface
{
    public function getAliveOpenChatIdAll(): array
    {
        return DB::fetchAll(
            'SELECT 
                oc.id,
                COALESCE(archive.archived_at, oc.created_at) AS updated_at
            FROM 
                open_chat AS oc
                LEFT JOIN (
                    SELECT
                        id,
                        MAX(archived_at) AS archived_at
                    FROM
                        open_chat_archive
                    GROUP BY
                        id
                ) AS archive ON oc.id = archive.id
            WHERE
                is_alive = 1'
        );
    }

    public function getRecordCount(bool $isAlive = true): int
    {
        return (int)DB::execute(
            'SELECT COUNT(*) FROM open_chat ' . ($isAlive ? 'WHERE is_alive = 1' : '')
        )->fetchColumn();
    }

    public function getDailyRankingRecordCount(): int
    {
        return (int)DB::execute(
            'SELECT COUNT(id) FROM statistics_ranking_day'
        )->fetchColumn();
    }

    public function getPastWeekRankingRecordCount(): int
    {
        return (int)DB::execute(
            'SELECT COUNT(id) FROM statistics_ranking_week'
        )->fetchColumn();
    }

    public function findAllOrderByIdDesc(
        int $startId,
        int $endId,
    ): array {
        $query =
            'SELECT
                id,
                name,
                url,
                img_url,
                description,
                member,
                category,
                created_at AS datetime,
                is_alive
            FROM
                open_chat
            ORDER BY
                id DESC
            LIMIT
                :startId, :limit';

        $limit = $endId - $startId;
        return DB::fetchAll($query, compact('startId', 'limit'));
    }

    public function findAllOrderByIdAscCreatedAtColumn(): array
    {
        $date = date('Y-m-d');

        $query =
            "SELECT
                CASE
                    WHEN YEAR(:date) = YEAR(`created_at`)
                    THEN DATE_FORMAT(`created_at`, '%m/%d %H時')
                    ELSE DATE_FORMAT(`created_at`, '%Y/%m/%d %H時')
                END AS `created_at`
            FROM
                open_chat
            ORDER BY
                id ASC";

        return DB::fetchAll($query, compact('date'), args: [\PDO::FETCH_COLUMN]);
    }

    public function findDeletedOrderByTimeDesc(
        int $startId,
        int $endId,
    ): array {
        $query =
            'SELECT
                id,
                name,
                url,
                img_url,
                description,
                member,
                category,
                updated_at AS deleted_at
            FROM
                open_chat
            WHERE
                is_alive = 0
            ORDER BY
                 DATE(updated_at) DESC, member DESC
            LIMIT
                :startId, :limit';

        $limit = $endId - $startId;
        return DB::fetchAll($query, compact('startId', 'limit'));
    }

    public function findDeletedOrderByTimeAscUpdatedAtColumn(): array
    {
        $date = date('Y-m-d');

        $query =
            "SELECT
                CASE
                    WHEN YEAR(:date) = YEAR(`updated_at`)
                    THEN DATE_FORMAT(`updated_at`, '%m/%d')
                    ELSE DATE_FORMAT(`updated_at`, '%Y/%m/%d')
                END AS `deleted_at`
            FROM
                open_chat
            WHERE
                is_alive = 0
            ORDER BY
                 DATE(updated_at) ASC, member ASC";

        return DB::fetchAll($query, compact('date'), args: [\PDO::FETCH_COLUMN]);
    }

    public function findRecentArchive(
        int $startId,
        int $endId,
    ): array {
        $query =
            "SELECT
                id,
                name,
                img_url,
                description,
                member,
                emblem,
                category,
                archived_at,
                updated_at AS archive_updated_at,
                update_img,
                update_description,
                update_name
            FROM
                open_chat_archive
            ORDER BY
                 DATE(archived_at) DESC, member DESC
            LIMIT
                :startId, :limit";

        $limit = $endId - $startId;
        return DB::fetchAll($query, compact('startId', 'limit'));
    }

    public function findRecentArchiveAscArchivedAtColumn(): array
    {
        $date = date('Y-m-d');

        $query =
            "SELECT
                CASE
                    WHEN YEAR(:date) = YEAR(archived_at)
                    THEN DATE_FORMAT(archived_at, '%m/%d')
                    ELSE DATE_FORMAT(archived_at, '%Y/%m/%d')
                END AS archived_at
            FROM
                open_chat_archive
            ORDER BY
                DATE(archived_at) ASC, member ASC";

        return DB::fetchAll($query, compact('date'), args: [\PDO::FETCH_COLUMN]);
    }

    public function findArchives(int $id): array
    {
        $query =
            "SELECT
                id,
                archive_id,
                group_id,
                name,
                description,
                img_url,
                member,
                updated_at AS archive_updated_at,
                emblem,
                update_description,
                update_img,
                update_name
            FROM
                open_chat_archive
            WHERE
                id = :id
            ORDER BY
                archive_id DESC";

        return DB::fetchAll($query, compact('id'));
    }

    public function getRankingRecordByMylist(array $idArray): array
    {
        $statements = array_fill(0, count($idArray), "?");
        $statement = "(" . implode(',', $statements) . ")";

        $query =
            "SELECT
                oc.id,
                oc.name,
                oc.url,
                oc.img_url,
                oc.description,
                oc.member,
                oc.emblem,
                oc.is_alive,
                ranking.diff_member AS diff_member,
                ranking.percent_increase AS percent_increase,
                ranking.id AS ranking_id
            FROM
                open_chat AS oc
                LEFT JOIN statistics_ranking_day AS ranking ON oc.id = ranking.open_chat_id
            WHERE
                oc.id IN {$statement}";

        DB::connect();
        $stmt = DB::$pdo->prepare($query);
        $stmt->execute($idArray);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findMemberStatsDailyRanking(
        int $startId,
        int $endId,
    ): array {
        return $this->findMemberStatsRanking($startId, $endId, 'statistics_ranking_day');
    }

    public function findMemberStatsPastWeekRanking(
        int $startId,
        int $endId,
    ): array {
        return $this->findMemberStatsRanking($startId, $endId, 'statistics_ranking_week');
    }

    protected function findMemberStatsRanking(
        int $startId,
        int $endId,
        string $tableName
    ): array {
        $query =
            "SELECT
                oc.id,
                oc.name,
                oc.url,
                oc.img_url,
                oc.description,
                oc.member,
                oc.emblem,
                oc.category,
                ranking.diff_member,
                ranking.percent_increase
            FROM
                open_chat AS oc
                JOIN (
                    SELECT
                        *
                    FROM
                        {$tableName}
                    WHERE
                        id > :startId
                        AND id <= :endId
                ) AS ranking ON oc.id = ranking.open_chat_id
            ORDER BY
                ranking.id ASC";

        return DB::fetchAll($query, compact('startId', 'endId'));
    }
}
