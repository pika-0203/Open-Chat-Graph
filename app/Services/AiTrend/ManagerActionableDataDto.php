<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

/**
 * 管理者向け実行可能データ転送オブジェクト
 * 「明日から実行できる具体的アクション」に特化
 */
class ManagerActionableDataDto
{
    public array $winningFormulas;          // 実証済み成功パターン
    public array $blueOceanOpportunities;   // 未開拓チャンス分野
    public array $operationalSecrets;       // 運営の秘訣
    public array $targetStrategies;         // ターゲット別戦略
    public array $immediateOpportunities;   // 今すぐのチャンス
    public array $avoidancePatterns;        // 失敗回避ガイド

    public function __construct(
        array $winningFormulas,
        array $blueOceanOpportunities,
        array $operationalSecrets,
        array $targetStrategies,
        array $immediateOpportunities,
        array $avoidancePatterns
    ) {
        $this->winningFormulas = $winningFormulas;
        $this->blueOceanOpportunities = $blueOceanOpportunities;
        $this->operationalSecrets = $operationalSecrets;
        $this->targetStrategies = $targetStrategies;
        $this->immediateOpportunities = $immediateOpportunities;
        $this->avoidancePatterns = $avoidancePatterns;
    }
}