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

    public function getRecordCount(): int
    {
        return (int)DB::execute(
            'SELECT COUNT(id) FROM open_chat WHERE is_alive = 1'
        )->fetchColumn();
    }

    public function getRecentArchiveRecordCount(): int
    {
        return (int)DB::execute(
            'SELECT
                COUNT(oc.id)
            FROM
                open_chat AS oc
                JOIN (
                    SELECT
                        id,
                        MAX(archive_id) AS archive_id
                    FROM
                        open_chat_archive
                    GROUP BY
                        id
                ) AS archive ON oc.id = archive.id'
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
                oc.id,
                oc.name,
                oc.url,
                oc.img_url,
                oc.description,
                oc.member,
                oc.category,
                oc.created_at AS datetime
            FROM
                open_chat AS oc
            WHERE
                is_alive = 1
            ORDER BY
                oc.id DESC
            LIMIT
                :startId, :limit';

        $limit = $endId - $startId;
        return DB::fetchAll($query, compact('startId', 'limit'));
    }

    public function findRecentArchive(
        int $startId,
        int $endId,
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
                oc.is_alive,
                oc.category,
                archive.archived_at AS date,
                archive.update_img,
                archive.update_description,
                archive.update_name
            FROM
                open_chat AS oc
                JOIN (
                    SELECT
                        id,
                        MAX(archive_id) AS archive_id,
                        MAX(group_id) AS group_id
                    FROM
                        open_chat_archive
                    GROUP BY
                        id
                ) AS archive2 ON oc.id = archive2.id
                JOIN open_chat_archive AS archive ON archive2.archive_id = archive.archive_id
            ORDER BY
                DATE(archive.archived_at) DESC, archive2.group_id DESC
            LIMIT
                :startId, :limit";

        $limit = $endId - $startId;
        return DB::fetchAll($query, compact('startId', 'limit'));
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
                archived_at,
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
