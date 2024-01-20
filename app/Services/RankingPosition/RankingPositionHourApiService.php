<?php

declare(strict_types=1);

namespace App\Services\RankingPosition;

use App\Config\AppConfig;
use App\Models\Repositories\RankingPosition\Dto\RankingPositionHourApiDto;
use App\Models\Repositories\RankingPosition\RankingPositionHourApiRepositoryInterface;

class RankingPositionHourApiService
{
    private const UPDATE_MINUTES = 50;
    private int $now;
    private RankingPositionHourApiRepositoryInterface $rankingPositionHourApiRepository;

    function __construct(RankingPositionHourApiRepositoryInterface $rankingPositionHourApiRepository)
    {
        $this->rankingPositionHourApiRepository = $rankingPositionHourApiRepository;
        $this->now = time();
    }

    function getNextUpdate(): \DateTime
    {
        $currentTime = new \DateTime('@' . $this->now);
        $currentTime->setTimeZone(new \DateTimeZone('Asia/Tokyo'));
        if ((int)$currentTime->format('i') >= self::UPDATE_MINUTES) {
            $currentTime->modify('+1 hour');
        }

        $currentTime->setTime((int)$currentTime->format('H'), self::UPDATE_MINUTES);
        return $currentTime;
    }

    function getTentativeNextUpdate(): \DateTime
    {
        $currentTime = new \DateTime('@' . $this->now);
        $currentTime->setTimeZone(new \DateTimeZone('Asia/Tokyo'));
        $currentTime->modify('+10 minute');
        return $currentTime;
    }

    function getLatestRanking(string $emid, int $category): RankingPositionHourApiDto|false
    {
        return $this->rankingPositionHourApiRepository->getLatestRanking($emid, $category, $this->getCurrentTime());
    }

    function getCurrentTime(): \DateTime
    {
        $currentTime = new \DateTime('@' . $this->now);
        $currentTime->setTimeZone(new \DateTimeZone('Asia/Tokyo'));
        if ((int)$currentTime->format('i') < AppConfig::CRON_START_MINUTE) {
            $currentTime->modify('-1 hour');
        }

        $currentTime->setTime((int)$currentTime->format('H'), AppConfig::CRON_START_MINUTE);
        return $currentTime;
    }
}
