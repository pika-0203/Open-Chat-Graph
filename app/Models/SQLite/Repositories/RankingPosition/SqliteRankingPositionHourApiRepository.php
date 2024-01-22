<?php

declare(strict_types=1);

namespace App\Models\SQLite\Repositories\RankingPosition;

use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourApiDto;
use App\Models\Repositories\RankingPosition\RankingPositionHourApiRepositoryInterface;
use App\Models\SQLite\SQLiteRankingPositionHour;

class SqliteRankingPositionHourApiRepository implements RankingPositionHourApiRepositoryInterface
{
    public function getLatestRanking(string $emid, int $category, \DateTime $time): RankingPositionHourApiDto|false
    {
        SQLiteRankingPositionHour::connect('?mode=ro&nolock=1');
        $dto = new RankingPositionHourApiDto;

        $risingTotal = $category === 0 ? 0 : $this->getTotalCount('rising', $category, $time);
        if ($category && !$risingTotal) {
            return false;
        }

        $risingAllTotal = $this->getTotalCount('rising', 0, $time);
        if (!$risingAllTotal) {
            return false;
        }

        $rankingTotal = $category === 0 ? 0 : $this->getTotalCount('ranking', $category, $time);
        if ($category && !$rankingTotal) {
            return false;
        }

        $rankingAllTotal = $this->getTotalCount('ranking', 0, $time);
        if (!$rankingAllTotal) {
            return false;
        }

        $dto->rising_total_count = $risingTotal;
        $dto->rising_all_total_count = $risingAllTotal;
        $dto->ranking_total_count = $rankingTotal;
        $dto->ranking_all_total_count = $rankingAllTotal;

        $dto->member = $category === 0 ? false : $this->getMember($emid, $time);
        if (!$dto->member) {
            SQLiteRankingPositionHour::$pdo = null;
            return $dto;
        }

        $dto->rising_position = $this->getPotision('rising', $emid, $category, $time);
        $dto->rising_all_position = $this->getPotision('rising', $emid, 0, $time);
        $dto->ranking_position = $this->getPotision('ranking', $emid, $category, $time);
        $dto->ranking_all_position = $this->getPotision('ranking', $emid, 0, $time);

        SQLiteRankingPositionHour::$pdo = null;
        return $dto;
    }

    private function getTotalCount(string $tableName, int $category, \DateTime $time): int|false
    {
        $timeString = $time->format('Y-m-d H:i:s');

        $result = SQLiteRankingPositionHour::fetchColumn(
            "SELECT
                count(*)
            FROM
                {$tableName}
            WHERE
                time = '{$timeString}'
                AND category = {$category}"
        );

        return $result ? $result : false;
    }

    private function getMember(string $emid, \DateTime $time): int|false
    {
        $timeString = $time->format('Y-m-d H:i:s');

        return SQLiteRankingPositionHour::fetchColumn(
            "SELECT
                member
            FROM
                ranking
            WHERE
                emid = '{$emid}'
                AND time = '{$timeString}'
            LIMIT
                1"
        ) ?? false;
    }

    private function getPotision(string $tableName, string $emid, int $category, \DateTime $time): int|false
    {
        $timeString = $time->format('Y-m-d H:i:s');

        return SQLiteRankingPositionHour::fetchColumn(
            "SELECT
                position
            FROM
                {$tableName}
            WHERE
                emid = '{$emid}'
                AND category = {$category}
                AND time = '{$timeString}'"
        ) ?? false;
    }
}
