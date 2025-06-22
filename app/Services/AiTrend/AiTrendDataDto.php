<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

/**
 * AIトレンド分析データの転送オブジェクト
 */
class AiTrendDataDto
{
    public array $risingChats;
    public array $categoryTrends;
    public array $tagTrends;
    public array $overallStats;
    public AiAnalysisDto $aiAnalysis;
    public array $historicalData;
    public array $realtimeMetrics;

    /**
     * @param array $risingChats 急成長チャットリスト
     * @param array $categoryTrends カテゴリトレンド
     * @param array $tagTrends タグトレンド
     * @param array $overallStats 全体統計
     * @param AiAnalysisDto $aiAnalysis AI分析結果
     * @param array $historicalData 時系列履歴データ
     * @param array $realtimeMetrics リアルタイム指標
     */
    public function __construct(
        array $risingChats,
        array $categoryTrends,
        array $tagTrends,
        array $overallStats,
        AiAnalysisDto $aiAnalysis,
        array $historicalData = [],
        array $realtimeMetrics = []
    ) {
        $this->risingChats = $risingChats;
        $this->categoryTrends = $categoryTrends;
        $this->tagTrends = $tagTrends;
        $this->overallStats = $overallStats;
        $this->aiAnalysis = $aiAnalysis;
        $this->historicalData = $historicalData;
        $this->realtimeMetrics = $realtimeMetrics;
    }
}
