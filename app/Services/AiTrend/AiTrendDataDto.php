<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

/**
 * AIトレンド分析データの転送オブジェクト（簡素化版）
 */
class AiTrendDataDto
{
    public array $risingChats;
    public array $tagTrends;
    public AiAnalysisDto $aiAnalysis;

    /**
     * @param array $risingChats 急成長チャットリスト
     * @param array $tagTrends タグトレンド
     * @param AiAnalysisDto $aiAnalysis AI分析結果
     */
    public function __construct(
        array $risingChats,
        array $tagTrends,
        AiAnalysisDto $aiAnalysis
    ) {
        $this->risingChats = $risingChats;
        $this->tagTrends = $tagTrends;
        $this->aiAnalysis = $aiAnalysis;
    }
}
