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
     * @return array `['weeklyStatistics' => ['date' => ['Y-m-d (曜日)'], 'member' => [int]], 'allStatistics' => ['date' => ['Y-m-d'], 'member' => [int]]` チャート向けにラベルとデータの配列が別けられている連想配列
     */
    function getStatisticsData(int $open_chat_id): array
    {
        $data = $this->statisticsRepository->getDailyStatisticsByPeriod(
            $open_chat_id,
            strtotime('2022-01-01'),
            time()
        );

        return [
            'weeklyStatistics' => $this->converetArrayForWeekData($data),
            'allStatistics' => $this->converetArrayForAllData($data)
        ];
    }

    /**
     * @return array `['date' => ['Y-m-d'], 'member' => [int]]` 全期間チャート向けにラベルとデータの配列が別けられている連想配列
     */
    private function converetArrayForAllData(array $data): array
    {
        $newArray = ['date' => [], 'member' => []];
        foreach ($data as $row) {
            $newArray['date'][] = $this->convertDateForAlldata($row['date']);
            $newArray['member'][] = (int)$row['member'];
        }
        return $newArray;
    }

    /**
     * `Y-m-d`から`Y/m/d`に変換する。`Y`が今年の場合は、`Y/`を省略して`m/d`にする。
     */
    private function convertDateForAlldata(string $date): string
    {
        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);
        $day = substr($date, 8, 2);

        if ($year === date('Y')) {
            return $month . '/' . $day;
        }
        return $year . '/' . $month . '/' . $day;
    }

    /**
     * @return array `['date' => ['Y-m-d (曜日)'], 'member' => [int]]` 週次のチャート向けにラベルとデータの配列が別けられている連想配列
     */
    private function converetArrayForWeekData(array $data): array
    {
        $array = array_reverse(array_slice($data, -90, 90, true));

        $newArray = ['date' => [], 'member' => []];
        foreach ($array as $row) {
            $newArray['date'][] = $this->convertDateForWeekData($row['date']);
            $newArray['member'][] = (int)$row['member'];
        }
        return $newArray;
    }

    /**
     * `Y-m-d`から`Y/m/d (曜日)`に変換する。`Y`が今年の場合は、`Y/`を省略して`m/d (曜日)`にする。
     */
    private function convertDateForWeekData(string $date): string
    {
        $week_ja = ["日", "月", "火", "水", "木", "金", "土"];

        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);
        $day = substr($date, 8, 2);

        $datetime = new \DateTime($date);
        $week = $week_ja[$datetime->format('w')];

        if ($year === date('Y')) {
            return $month . '/' . $day . ' (' . $week . ')';
        }
        return $year . '/' . $month . '/' . $day . ' (' . $week . ')';
    }
}
