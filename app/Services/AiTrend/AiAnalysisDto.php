<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

/**
 * AIトレンド分析結果の転送オブジェクト（簡素化版）
 */
class AiAnalysisDto
{
    public string $summary;

    public function __construct(string $summary)
    {
        $this->summary = $summary;
    }
}
