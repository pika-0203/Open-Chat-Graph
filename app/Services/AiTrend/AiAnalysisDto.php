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

    public function __construct(
        string $summary,
        array $insights,
        array $predictions,
        array $recommendations
    ) {
        $this->summary = $summary;
        $this->insights = $insights;
        $this->predictions = $predictions;
        $this->recommendations = $recommendations;
    }
}
