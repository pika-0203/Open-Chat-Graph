<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

/**
 * AI分析結果の転送オブジェクト
 */
class AiAnalysisDto
{
    public string $summary;
    public array $insights;
    public array $predictions;
    public array $recommendations;

    /**
     * @param string $summary 分析サマリー
     * @param array $insights 分析洞察
     * @param array $predictions 予測
     * @param array $recommendations 推奨事項
     */
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
