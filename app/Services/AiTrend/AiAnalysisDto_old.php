<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

/**
 * 実用的なAI分析結果の転送オブジェクト
 */
class AiAnalysisDto
{
    public string $summary;
    public array $insights;
    public array $predictions;
    public array $recommendations;
    
    // 拡張可能なメタデータフィールド（現在は使用していないが将来のため）
    public array $growthPatterns;
    public array $categoryInsights;
    public array $anomalies;
    public array $timePatterns;
    public array $membershipTrends;
    public array $metadata;
    public string $aiComment;

    public function __construct(
        string $summary,
        array $insights,
        array $predictions,
        array $recommendations,
        array $growthPatterns = [],
        array $categoryInsights = [],
        array $anomalies = [],
        array $timePatterns = [],
        array $membershipTrends = [],
        array $metadata = [],
        string $aiComment = ''
    ) {
        $this->summary = $summary;
        $this->insights = $insights;
        $this->predictions = $predictions;
        $this->recommendations = $recommendations;
        $this->growthPatterns = $growthPatterns;
        $this->categoryInsights = $categoryInsights;
        $this->anomalies = $anomalies;
        $this->timePatterns = $timePatterns;
        $this->membershipTrends = $membershipTrends;
        $this->metadata = $metadata;
        $this->aiComment = $aiComment;
    }
}
    ) {
        $this->summary = $summary;
        $this->insights = $insights;
        $this->predictions = $predictions;
        $this->recommendations = $recommendations;
        $this->growthPatterns = $growthPatterns;
        $this->categoryInsights = $categoryInsights;
        $this->anomalies = $anomalies;
        $this->timePatterns = $timePatterns;
        $this->membershipTrends = $membershipTrends;
        $this->realTimeVibes = $realTimeVibes;
        $this->aiComment = $aiComment;
    }
}
