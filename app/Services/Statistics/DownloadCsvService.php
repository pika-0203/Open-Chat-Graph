<?php

declare(strict_types=1);

namespace App\Services\Statistics;

use App\Config\AppConfig;
use App\Models\Repositories\Statistics\StatisticsPageRepositoryInterface;
use App\Services\OpenChat\Enum\RankingType;
use App\Services\RankingPosition\RankingPositionChartArrayService;

class DownloadCsvService
{
    function __construct(
        private StatisticsPageRepositoryInterface $statisticsRepository,
        private StatisticsChartArrayService $statisticsChartArrayService,
        private RankingPositionChartArrayService $rankingPositionChartArrayService,
    ) {
    }

    function sendCsv(int $open_chat_id, int $category, string $name)
    {
        [$column, $result] = $this->{$category ? 'buildData' : 'buildDataNoCategory'}($open_chat_id, $category);

        $filename = '[OC_Graph]' . $this->sanitizeFileName($name);
        header('Content-Type: text/csv; charset=UTF-16LE');
        header("Content-Disposition: attachment; filename*=UTF-8''" . $filename . '.csv');

        $output = fopen('php://output', 'wb');

        // UTF-16LEのBOMを出力
        fwrite($output, pack('C*', 0xFF, 0xFE));

        // カラムヘッダーのエンコーディング変換と出力
        $encoded_column = mb_convert_encoding(implode("\t", $column) . "\r\n", 'UTF-16LE', 'UTF-8');
        fwrite($output, $encoded_column);

        // 各行のデータのエンコーディング変換と出力
        foreach ($result as $row) {
            $encoded_row = mb_convert_encoding(implode("\t", $row) . "\r\n", 'UTF-16LE', 'UTF-8');
            fwrite($output, $encoded_row);
        }

        fclose($output);
    }

    function buildData(int $open_chat_id, int $category): array
    {
        $statsDto = $this->statisticsChartArrayService->buildStatisticsChartArray($open_chat_id);

        $dtos = [];
        for ($i = 1; $i <= 4; $i++) {
            $type = $i % 2 !== 0 ? RankingType::Rising : RankingType::Ranking;
            $dtos[] = $this->rankingPositionChartArrayService->getRankingPositionChartArray(
                $type,
                $open_chat_id,
                $i < 3 ? $category : 0,
                new \DateTime($statsDto->startDate),
                new \DateTime($statsDto->endDate)
            );
        }

        $column = [
            '日付',
            'メンバー数',
            array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category] . '_公式急上昇順位_中央値',
            array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category] . '_公式急上昇順位_中央値_時間帯',
            array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category] . '_公式急上昇順位_中央値_全体件数',
            array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category] . '_公式ランキング順位_中央値',
            array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category] . '_公式ランキング順位_中央値_時間帯',
            array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category] . '_公式ランキング順位_中央値_全体件数',
            '全カテゴリ_公式急上昇順位_最頻値',
            '全カテゴリ_公式急上昇順位_最頻値_時間帯',
            '全カテゴリ_公式急上昇順位_最頻値_全体件数',
            '全カテゴリ_公式ランキング順位_中央値',
            '全カテゴリ_公式ランキング順位_中央値_全体件数',
        ];

        $result = [];
        foreach ($statsDto->date as $key => $date) {
            $result[] = [
                $statsDto->date[$key],
                $statsDto->member[$key],
                $dtos[0]->position[$key],
                $dtos[0]->time[$key],
                $dtos[0]->totalCount[$key],
                $dtos[1]->position[$key],
                $dtos[1]->totalCount[$key],
                $dtos[2]->position[$key],
                $dtos[2]->time[$key],
                $dtos[2]->totalCount[$key],
                $dtos[3]->position[$key],
                $dtos[3]->totalCount[$key],
            ];
        }

        return [$column, $result];
    }

    function buildDataNoCategory(int $open_chat_id, int $category): array
    {
        $statsDto = $this->statisticsChartArrayService->buildStatisticsChartArray($open_chat_id);

        $dto = $this->rankingPositionChartArrayService->getRankingPositionChartArray(
            RankingType::Rising,
            $open_chat_id,
            0,
            new \DateTime($statsDto->startDate),
            new \DateTime($statsDto->endDate)
        );

        $column = [
            '日付',
            'メンバー数',
            '全カテゴリ_公式急上昇順位_最頻値',
            '全カテゴリ_公式急上昇順位_最頻値_時間帯',
            '全カテゴリ_公式急上昇順位_最頻値_全体件数',
        ];

        $result = [];
        foreach ($statsDto->date as $key => $date) {
            $result[] = [
                $statsDto->date[$key],
                $statsDto->member[$key],
                $dto->position[$key],
                $dto->time[$key],
                $dto->totalCount[$key],
            ];
        }

        return [$column, $result];
    }

    private function sanitizeFileName(string $name): string
    {
        // 無効な文字を削除または置換
        $replacedString = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $name);
        $replacedString = preg_replace('/[<>:"\/\\|?*\x00-\x1F]/', '', $name);

        $filename = rawurlencode($replacedString);

        return $filename;
    }
}
