<?php

declare(strict_types=1);

namespace App\Services\Statistics;

use App\Models\Repositories\Statistics\StatisticsPageRepositoryInterface;

class DownloadCsvService
{
    function __construct(
        private StatisticsPageRepositoryInterface $statisticsRepository
    ) {
    }

    function sendCsv(int $open_chat_id, string $name)
    {
        $statisticsData = $this->statisticsRepository->getDailyStatsByIdForCsv($open_chat_id);

        $filename = '[OC_Graph]' . $this->sanitizeFileName($name);
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename*=UTF-8''" . $filename . '.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['date', 'member']);

        foreach ($statisticsData as $row) {
            $data = [$row['date'], $row['member']];
            fputcsv($output, $data);
        }
    }

    static function sanitizeFileName(string $name): string
    {
        // 無効な文字を削除または置換
        $replacedString = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $name);
        $replacedString = preg_replace('/[<>:"\/\\|?*\x00-\x1F]/', '', $name);

        $filename = rawurlencode($replacedString);

        return $filename;
    }
}
