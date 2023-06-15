<?php

declare(strict_types=1);

namespace App\Services\Statistics;

use App\Models\Repositories\StatisticsRepositoryInterface;

class StatisticsService
{
    private StatisticsRepositoryInterface $statisticsRepository;

    function __construct(StatisticsRepositoryInterface $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    /**
     * @return array `['date' => ['Y-m-d'], 'member' => [int]]` チャート向けにラベルとデータの配列が別けられている連想配列
     */
    function getStatisticsData(int $open_chat_id): array
    {
        return $this->statisticsRepository->getDailyStatisticsByPeriod(
            $open_chat_id,
            strtotime('2022-01-01'),
            time()
        );
    }
}
