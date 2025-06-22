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

    /**
     * @param array $risingChats 急成長チャットリスト
     * @param array $categoryTrends カテゴリトレンド
     * @param array $tagTrends タグトレンド
     * @param array $overallStats 全体統計
     * @param AiAnalysisDto $aiAnalysis AI分析結果
     */
    public function __construct(
        array $risingChats,
        array $categoryTrends,
        array $tagTrends,
        array $overallStats,
        AiAnalysisDto $aiAnalysis
    ) {
        $this->risingChats = $risingChats;
        $this->categoryTrends = $categoryTrends;
        $this->tagTrends = $tagTrends;
        $this->overallStats = $overallStats;
        $this->aiAnalysis = $aiAnalysis;
    }
}
