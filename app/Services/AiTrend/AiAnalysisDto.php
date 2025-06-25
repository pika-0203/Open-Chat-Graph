<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

/**
 * AIトレンド分析結果の転送オブジェクト（分割API版）
 */
class AiAnalysisDto
{
    public string $summary;
    public array $insights;

    public function __construct(
        string $summary,
        array $insights
    ) {
        $this->summary = $summary;
        $this->insights = $insights;
    }
}
