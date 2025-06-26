<?php

use App\Services\AiTrend\AiTrendDataDto;
use App\Services\AiTrend\Helpers\AiTrendInsightHelper;

/** @var AiTrendDataDto $aiTrendData */
$risingChats = $aiTrendData->risingChats;
$tagTrends = $aiTrendData->tagTrends;
$aiAnalysis = $aiTrendData->aiAnalysis;

?>

<style>
    .trend-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .trend-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .trend-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        padding: 24px;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .trend-title {
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .chat-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 12px;
    }
    
    .chat-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #f9fafb;
        border-radius: 6px;
    }
    
    .chat-rank {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }
    
    .chat-info {
        flex: 1;
        min-width: 0;
    }
    
    .chat-name {
        font-weight: 600;
        color: #1f2937;
        text-decoration: none;
        display: block;
        margin-bottom: 2px;
    }
    
    .chat-name:hover {
        color: #3b82f6;
    }
    
    .chat-growth {
        color: #059669;
        font-weight: 600;
        font-size: 14px;
    }
    
    .tag-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .tag-item {
        background: #eff6ff;
        color: #1d4ed8;
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s ease;
    }
    
    .tag-item:hover {
        background: #dbeafe;
        color: #1e40af;
        text-decoration: none;
    }
    
    .alert-item {
        padding: 16px;
        margin-bottom: 12px;
        border-radius: 6px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .alert-info { background: #f0f9ff; border-left: 4px solid #3b82f6; }
    .alert-warning { background: #fefbf2; border-left: 4px solid #f59e0b; }
    .alert-critical { background: #fef2f2; border-left: 4px solid #ef4444; }
    
    .recommendation-item {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
    }
    
    .recommendation-theme {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    .recommendation-detail {
        color: #4b5563;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .ai-insight {
        background: #f0f9ff;
        color: #1e40af;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 13px;
        margin-top: 8px;
        border-left: 3px solid #3b82f6;
        line-height: 1.4;
    }

    .ai-score {
        font-size: 12px;
        color: #6b7280;
        margin-top: 6px;
        padding: 4px 8px;
        background: #f9fafb;
        border-radius: 4px;
    }

    .potential-high { color: #dc2626; font-weight: 600; }
    .potential-medium { color: #ea580c; font-weight: 600; }
    .potential-low { color: #65a30d; font-weight: 600; }
    .potential-breakthrough { color: #dc2626; font-weight: 700; }
    .potential-disruptive { color: #c2410c; font-weight: 700; }
    .potential-innovative { color: #2563eb; font-weight: 600; }
    .potential-emerging { color: #059669; font-weight: 600; }
</style>

<section class="trend-container">
    <!-- ヘッダー -->
    <div class="trend-header">
        <h2 class="trend-title">AI分析ダッシュボード</h2>
        <p>オープンチャット成長トレンド分析</p>
    </div>

    <!-- AI分析注目トピックチャット -->
    <?php if (!empty($risingChats)): ?>
        <div class="trend-card">
            <h3 class="section-title">🧠 AI分析注目トピックチャット</h3>
            <p style="color: #6b7280; font-size: 14px; margin-bottom: 16px;">
                既存ランキングでは発見できない、<strong>14の高度分析手法</strong>とAI戦略判断により厳選された隠れた成長機会：<br>
                🚀爆発的成長 🌱ブレイクアウト直前 ⚡急加速成長 🔍異常成長検出 🎯AI成長予測 💎穴場市場<br>
                📈長期安定成長 🔄周期的パターン 💥勢い急上昇 ✨隠れた優良株 ⏰ブレイクタイミング<br>
                🔮将来成長ポテンシャル 🌟新興トレンドトピック
            </p>
            <div class="chat-list">
                <?php foreach (array_slice($risingChats, 0, 5) as $index => $chat): ?>
                    <?php if (!isset($chat['id']) || !isset($chat['name'])) continue; ?>
                    <div class="chat-item">
                        <div class="chat-rank"><?php echo (int)$index + 1 ?></div>
                        <div class="chat-info">
                            <a href="<?php echo url('/oc/' . $chat['id']) ?>" class="chat-name">
                                <?php echo htmlspecialchars($chat['name']) ?>
                            </a>
                            <div class="chat-growth">
                                (<?php echo htmlspecialchars($chat['category'] ?? 'その他') ?>)
                            </div>
                            
                            <!-- AI選出理由 -->
                            <?php if (!empty($chat['selection_rationale'])): ?>
                                <div class="ai-insight">
                                    🎯 <strong>AI選出理由:</strong> <?php echo htmlspecialchars($chat['selection_rationale']) ?>
                                </div>
                            <?php elseif (!empty($chat['attention_magnetism'])): ?>
                                <div class="ai-insight">
                                    💡 <?php echo htmlspecialchars($chat['attention_magnetism']) ?>
                                </div>
                            <?php else: ?>
                                <div class="ai-insight">
                                    🔥 <?php echo AiTrendInsightHelper::generateInsightText($chat) ?>
                                </div>
                            <?php endif ?>

                            <!-- 分析ソース表示 -->
                            <?php if (!empty($chat['selection_source'])): ?>
                                <div class="ai-insight" style="background: #f3f4f6; color: #4b5563; margin-top: 4px;">
                                    📊 分析手法: <?php 
                                        $source = $chat['analysis_reason'] ?? $chat['selection_source'];
                                        $sourceMap = [
                                            // 成長段階別分析
                                            'viral_pattern' => '🚀 爆発的成長パターン',
                                            'pre_viral' => '🌱 ブレイクアウト直前',
                                            'real_time_acceleration' => '⚡ 急加速成長中',
                                            
                                            // 特殊分析手法
                                            'anomaly' => '🔍 異常成長検出',
                                            'trend_prediction' => '🎯 AI成長予測',
                                            'low_competition_segment' => '💎 穴場市場発見',
                                            
                                            // 長期・安定性分析
                                            'long_term_trend' => '📈 長期安定成長',
                                            'seasonal_pattern' => '🔄 周期的成長パターン',
                                            'recovery_pattern' => '🔄 復活・回復パターン',
                                            
                                            // 新規追加分析手法
                                            'momentum_surge' => '💥 成長勢い急上昇',
                                            'hidden_gem' => '✨ 隠れた優良株',
                                            'breakthrough_timing' => '⏰ ブレイクタイミング',
                                            'market_disruption' => '🌊 市場破壊的成長',
                                            'community_magnetism' => '🧲 コミュニティ磁力',
                                            'exponential_curve' => '📊 指数関数的成長',
                                            
                                            // 新規追加の将来性分析手法
                                            'future_growth_potential' => '🔮 将来成長ポテンシャル',
                                            'emerging_trend_topic' => '🌟 新興トレンドトピック'
                                        ];
                                        echo htmlspecialchars($sourceMap[$source] ?? $source);
                                    ?>
                                </div>
                            <?php endif ?>
                            
                            <!-- AI分析スコア表示 -->
                            <?php if (!empty($chat['ai_insight_score'])): ?>
                                <div class="ai-score">
                                    <div>AI分析スコア: <strong><?php echo $chat['ai_insight_score'] ?>点</strong>
                                    <?php if (!empty($chat['growth_potential'])): ?>
                                        | 成長性: <span class="potential-<?php echo $chat['growth_potential'] ?>">
                                            <?php
                                            $potentialLabels = [
                                                'breakthrough' => '突破的成長',
                                                'high' => '高成長',
                                                'emerging' => '新興成長',
                                                'disruptive' => '破壊的革新',
                                                'innovative' => '革新的成長',
                                                'stable_long_term' => '長期安定',
                                                'recovery_momentum' => '回復成長'
                                            ];
                                            echo $potentialLabels[$chat['growth_potential']] ?? $chat['growth_potential'];
                                            ?>
                                        </span>
                                    <?php endif ?>
                                    </div>
                                    
                                    <!-- スコア内訳表示 -->
                                    <?php if (!empty($chat['score_breakdown'])): ?>
                                        <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                                            📊 スコア内訳：
                                            <?php 
                                            $breakdown = is_string($chat['score_breakdown']) ? json_decode($chat['score_breakdown'], true) : $chat['score_breakdown'];
                                            if (is_array($breakdown)): 
                                                $breakdownLabels = [
                                                    'growth_momentum' => '成長勢い',
                                                    'market_opportunity' => '市場機会',
                                                    'uniqueness_factor' => '独自性',
                                                    'timing_value' => 'タイミング',
                                                    'sustainability' => '持続性'
                                                ];
                                                $parts = [];
                                                foreach ($breakdown as $key => $value) {
                                                    $label = $breakdownLabels[$key] ?? $key;
                                                    $parts[] = $label . ':' . $value;
                                                }
                                                echo implode(' | ', $parts);
                                            endif;
                                            ?>
                                        </div>
                                    <?php endif ?>
                                </div>
                            <?php endif ?>

                            <!-- 隠れた価値分析 -->
                            <?php if (!empty($chat['hidden_value_analysis'])): ?>
                                <div class="ai-insight" style="background: #ecfdf5; border-left-color: #10b981; margin-top: 4px;">
                                    💎 <strong>隠れた価値:</strong> <?php echo htmlspecialchars($chat['hidden_value_analysis']) ?>
                                </div>
                            <?php endif ?>

                            <!-- 将来予測 -->
                            <?php if (!empty($chat['future_prediction'])): ?>
                                <div class="ai-insight" style="background: #fef3c7; border-left-color: #f59e0b; margin-top: 4px;">
                                    🔮 <strong>3ヶ月予測:</strong> <?php echo htmlspecialchars($chat['future_prediction']) ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- トレンドタグ -->
    <?php if (!empty($tagTrends)): ?>
        <div class="trend-card">
            <h3 class="section-title">🏷️ AI選出トレンドタグ</h3>
            <p style="color: #6b7280; font-size: 14px; margin-bottom: 16px;">
                単純な統計ランキングではなく、AIが戦略的価値と将来性を分析して厳選したトレンドタグ
            </p>
            <div class="tag-list">
                <?php foreach (array_slice($tagTrends, 0, 15) as $tag): ?>
                        <div style="position: relative; display: inline-block;">
                            <a href="<?php echo url('recommend?tag=' . urlencode(htmlspecialchars_decode($tag['tag']))) ?>" 
                               class="tag-item" 
                               title="<?php echo htmlspecialchars($tag['ai_rationale'] ?? $tag['strategic_value'] ?? '') ?>">
                                #<?php echo htmlspecialchars($tag['tag']) ?>
                                <?php if (!empty($tag['growth_potential']) && $tag['growth_potential'] === 'high'): ?>
                                    <span style="color: #dc2626;">🔥</span>
                                <?php elseif (!empty($tag['growth_potential']) && $tag['growth_potential'] === 'emerging'): ?>
                                    <span style="color: #059669;">💎</span>
                                <?php endif ?>
                            </a>
                            <?php if (!empty($tag['ai_rationale'])): ?>
                                <div class="ai-insight" style="position: absolute; top: 100%; left: 0; z-index: 10; width: 200px; margin-top: 4px; display: none; font-size: 12px; padding: 6px;">
                                    🤖 <?php echo htmlspecialchars($tag['ai_rationale']) ?>
                                </div>
                            <?php endif ?>
                        </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>


    <!-- 分析サマリー -->
    <?php if (!empty($aiAnalysis->summary)): ?>
        <div class="trend-card">
            <h3 class="section-title">📊 分析サマリー</h3>
            <div style="background: #f8fafc; padding: 16px; border-radius: 6px; border-left: 4px solid #3b82f6; line-height: 1.6;">
                <?php echo htmlspecialchars($aiAnalysis->summary) ?>
            </div>
        </div>
    <?php endif ?>
</section>