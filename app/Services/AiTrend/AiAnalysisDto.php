<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

/**
 * AIトレンド分析結果の転送オブジェクト
 */
class AiAnalysisDto
{
    public string $summary;
    public array $insights;
    public array $predictions;
    public array $recommendations;
    public array $anomalies;
    public array $alerts;
    public array $timeSeriesForecasts;

    public function __construct(
        string $summary,
        array $insights,
        array $predictions,
        array $recommendations,
        array $anomalies = [],
        array $alerts = [],
        array $timeSeriesForecasts = []
    ) {
        $this->summary = $summary;
        $this->insights = $insights;
        $this->predictions = $predictions;
        $this->recommendations = $recommendations;
        $this->anomalies = $anomalies;
        $this->alerts = $alerts;
        $this->timeSeriesForecasts = $timeSeriesForecasts;
    }
}
