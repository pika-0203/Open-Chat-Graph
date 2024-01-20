<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Persistence;

use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;
use App\Models\Repositories\RankingPosition\RankingPositionRepositoryInterface;
use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepository;
use DateTime;

class RankingPositionDailyPersistence
{
    function __construct(
        private RankingPositionHourRepositoryInterface $rankingPositionHourRepository,
        private RankingPositionRepositoryInterface $rankingPositionRepository,
        private OpenChatDataForUpdaterWithCacheRepository $openChatDataForUpdaterWithCacheRepository,
    ) {
    }

    function persistHourToDaily(\DateTime $date = new DateTime('yesterday')): void
    {
        $this->insert(
            $date,
            $this->rankingPositionHourRepository->getMinRankingHour(...),
            $this->rankingPositionRepository->insertDailyRankingPosition(...)
        );

        $this->insert(
            $date,
            $this->rankingPositionHourRepository->getMinRisingHour(...),
            $this->rankingPositionRepository->insertDailyRisingPosition(...)
        );

        $this->rankingPositionRepository->insertTotalCount(
            $this->rankingPositionHourRepository->getTotalCount($date)
        );

        $this->rankingPositionHourRepository->dalete($date);
    }

    /**
     * @param \Closure $getter $getter(\DateTime $date, bool $all = false): array
     *        `[['emid' => string, 'category' => int, 'position' => int, 'time' => stirng]]`
     * 
     * @param \Closure $inserter $inserter(array $rankingHourArray)
     *        `[['open_chat_id' => int, 'category' => int, 'position => int, 'time' => stirng]]`
     * 
     */
    private function insert(\DateTime $date, \Closure $getter, \Closure $inserter)
    {
        $inserter($this->convertElemets($getter($date)));
        $inserter($this->convertElemets($getter($date, true)));
    }

    private function convertElemets(array $elements): array
    {
        $convertedElements = [];
        foreach ($elements as $element) {
            $converted = $this->emidToOpenChatId($element);
            if (!$converted) {
                continue;
            }

            $convertedElements[] = $converted;
        }

        return $convertedElements;
    }

    private function emidToOpenChatId(array $element): array|false
    {
        $result = $this->openChatDataForUpdaterWithCacheRepository->getOpenChatIdByEmid($element['emid']);
        if (!$result) {
            return false;
        }

        unset($element['emid']);
        $element['open_chat_id'] = $result['id'];

        return $element;
    }
}
