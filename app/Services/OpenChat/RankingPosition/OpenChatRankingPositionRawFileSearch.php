<?php

declare(strict_types=1);

namespace App\Services\OpenChat\RankingPosition;

use App\Config\AppConfig;

class OpenChatRankingPositionRawFileSearch
{
    const CRON_START_MINUTES = 30;

    function getTentativeNextUpdate(int $timeToUpdateMinutes = 50): string
    {
        $currentTime = new \DateTime('now');
        $currentTime->setTimeZone(new \DateTimeZone('Asia/Tokyo'));
        if ((int)$currentTime->format('i') >= $timeToUpdateMinutes) {
            $currentTime->modify('+1 hour');
        }

        $currentTime->setTime((int)$currentTime->format('H'), $timeToUpdateMinutes);

        return $currentTime->format(\DateTime::ATOM);
    }

    /**
     * @return array|false - [
     *    rankingCaches => [  
     *      'rising' => array,
     *      'risingAll' => array,
     *      'ranking' => array,
     *      'rankingAll' => array
     *    ],
     *    'updatedAt' => string,
     *    'nextUpdate' => string
     *  ]
     */
    function getLatestRankingRawCache(int $category, int $timeToUpdateMinutes = 50): array|string
    {
        $categoryFile = "/{$category}.dat";
        $allFile = "/0.dat";

        $files = [
            'rising' => AppConfig::OPEN_CHAT_RISING_POSITION_DIR . $categoryFile,
            'risingAll' => AppConfig::OPEN_CHAT_RISING_POSITION_DIR . $allFile,
            'ranking' => AppConfig::OPEN_CHAT_RANKING_POSITION_DIR . $categoryFile,
            'rankingAll' => AppConfig::OPEN_CHAT_RANKING_POSITION_DIR . $allFile,
        ];

        $rankingCaches = array_combine(
            array_keys($files),
            array_map(fn ($path): array|false => getUnserializedArrayFromFile($path, true), $files)
        );

        $isValidRankingCaches = count(array_filter($rankingCaches, fn ($file) => $file === false)) === 0;
        if (!$isValidRankingCaches) {
            return $this->getTentativeNextUpdate2($timeToUpdateMinutes);
        }

        $modifiedFileTimes = array_map(fn ($file) => $this->getModifiedFileTime($file)->format('H'), $files);
        $isConsistentFiles = count(array_unique($modifiedFileTimes)) === 1;
        if (!$isConsistentFiles) {
            return $this->getTentativeNextUpdate2($timeToUpdateMinutes);
        }

        $updatedAt = fn () => $this->getModifiedFileTime($files['rising']);

        if (!$this->isLatestRankingCaches($updatedAt())) {
            return $this->getTentativeNextUpdate($timeToUpdateMinutes);
        }

        return [
            'rankingCaches' => $rankingCaches,
            'updatedAt' => $updatedAt()->format(\DateTime::ATOM),
            'nextUpdate' => $this->getNextUpdate($updatedAt(), $timeToUpdateMinutes),
        ];
    }

    private function getTentativeNextUpdate2(): string
    {
        $currentTime = new \DateTime('now');
        $currentTime->setTimeZone(new \DateTimeZone('Asia/Tokyo'));
        $currentTime->modify('+10 minute');
        return $currentTime->format(\DateTime::ATOM);
    }

    private function getModifiedFileTime(string $fileName): \DateTime
    {
        $fileTime = new \DateTime('@' . filemtime($fileName));
        $fileTime->setTimeZone(new \DateTimeZone('Asia/Tokyo'));
        if ((int)$fileTime->format('i') < self::CRON_START_MINUTES) {
            $fileTime->modify('-1 hour');
        }

        $fileTime->setTime((int)$fileTime->format('H'), self::CRON_START_MINUTES);
        return $fileTime;
    }

    private function isLatestRankingCaches(\DateTime $updatedAt): bool
    {
        $now = new \DateTime('now');
        $now->setTimeZone(new \DateTimeZone('Asia/Tokyo'));
        if ($now->format('H') === $updatedAt->format('H')) {
            return true;
        }

        if ((int)$now->format('i') < self::CRON_START_MINUTES) {
            $now->modify('-1 hour');
        }

        if ($now->format('H') === $updatedAt->format('H')) {
            return true;
        }

        return false;
    }

    private function getNextUpdate(\DateTime $updatedAt, int $timeToUpdateMinutes): string
    {
        $updatedAt->modify('+1 hour');
        $updatedAt->setTime((int)$updatedAt->format('H'), $timeToUpdateMinutes);
        $updatedAt->format(\DateTime::ATOM);
        return $updatedAt->format(\DateTime::ATOM);
    }

    /**
     * @return array
     * ```php
     * [
     *    'rising' => [$rising_position, $rising_total_count],
     *    'risingAll' => [$rising_all_position, $rising_all_total_count],
     *    'ranking' => [$ranking_position, $ranking_total_count],
     *    'rankingAll' => [$ranking_all_position, $ranking_all_total_count]
     * ] = $searchResults;
     * ```
     */
    function searchPositionFromEmid(array $rankingCaches, string $emid)
    {
        return array_map(function (array $data) use ($emid) {
            $index = array_search($emid, $data);
            $position = is_int($index) ? $index + 1 : false;
            $total = count($data);
            return [$position, $total];
        }, $rankingCaches);
    }
}
