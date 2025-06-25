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
                人智を超えたAI分析により選出された、実際に成長可能性が高い注目トピック
            </p>
            <div class="chat-list">
                <?php foreach (array_slice($risingChats, 0, 10) as $index => $chat): ?>
                    <?php if (!isset($chat['id']) || !isset($chat['name'])) continue; ?>
                    <div class="chat-item">
                        <div class="chat-rank"><?php echo (int)$index + 1 ?></div>
                        <div class="chat-info">
                            <a href="<?php echo url('/oc/' . $chat['id']) ?>" class="chat-name">
                                <?php echo htmlspecialchars($chat['name']) ?>
                            </a>
                            <div class="chat-growth">
                                +<?php echo number_format((int)($chat['growth_amount'] ?? $chat['week_growth_amount'] ?? 0)) ?>人 
                                (<?php echo htmlspecialchars($chat['category'] ?? 'その他') ?>)
                            </div>
                            
                            <!-- AI考察コメント -->
                            <?php if (!empty($chat['selection_rationale']) || !empty($chat['attention_magnetism'])): ?>
                                <div class="ai-insight">
                                    💡 <?php echo htmlspecialchars($chat['selection_rationale'] ?? $chat['attention_magnetism'] ?? 'AI分析により選出') ?>
                                </div>
                            <?php else: ?>
                                <div class="ai-insight">
                                    🔥 <?php echo AiTrendInsightHelper::generateInsightText($chat) ?>
                                </div>
                            <?php endif ?>
                            
                            <!-- AI分析スコア表示 -->
                            <?php if (!empty($chat['ai_insight_score'])): ?>
                                <div class="ai-score">
                                    AI分析スコア: <strong><?php echo $chat['ai_insight_score'] ?>点</strong>
                                    <?php if (!empty($chat['growth_potential']) || !empty($chat['revolutionary_potential'])): ?>
                                        | 成長性: <span class="potential-<?php echo $chat['growth_potential'] ?? $chat['revolutionary_potential'] ?>">
                                            <?php echo AiTrendInsightHelper::generateInsightText($chat['growth_potential'] ?? $chat['revolutionary_potential'] ?? '') ?>
                                        </span>
                                    <?php endif ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- AI分析結果 -->
    <?php if (!empty($aiAnalysis->alerts)): ?>
        <div class="trend-card">
            <h3 class="section-title">🤖 AI分析結果</h3>
            
            <?php foreach ($aiAnalysis->alerts as $alert): ?>
                <?php 
                $alertClass = 'alert-info';
                if (isset($alert['level'])) {
                    $alertClass = 'alert-' . $alert['level'];
                }
                ?>
                <div class="alert-item <?php echo $alertClass ?>">
                    <span style="font-size: 20px;"><?php echo $alert['icon'] ?? '💡' ?></span>
                    <div>
                        <h4 style="font-weight: 600; margin: 0 0 8px 0;">
                            <?php echo htmlspecialchars($alert['title'] ?? '') ?>
                        </h4>
                        <p style="margin: 0; line-height: 1.5;">
                            <?php echo htmlspecialchars($alert['message'] ?? '') ?>
                        </p>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <!-- AI推奨テーマ -->
    <?php if (!empty($aiAnalysis->recommendations)): ?>
        <div class="trend-card">
            <h3 class="section-title">🎯 AI推奨テーマ</h3>
            
            <?php foreach (array_slice($aiAnalysis->recommendations, 0, 5) as $rec): ?>
                <div class="recommendation-item">
                    <div class="recommendation-theme">
                        <?php echo htmlspecialchars($rec['theme'] ?? '') ?>
                    </div>
                    
                    <?php if (isset($rec['target'])): ?>
                    <div class="recommendation-detail">
                        <strong>ターゲット:</strong> <?php echo htmlspecialchars($rec['target']) ?>
                    </div>
                    <?php endif ?>
                    
                    <?php if (isset($rec['strategy'])): ?>
                    <div class="recommendation-detail">
                        <strong>戦略:</strong> <?php echo htmlspecialchars($rec['strategy']) ?>
                    </div>
                    <?php endif ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <!-- トレンドタグ -->
    <?php if (!empty($tagTrends)): ?>
        <div class="trend-card">
            <h3 class="section-title">🏷️ トレンドタグ</h3>
            <div class="tag-list">
                <?php foreach (array_slice($tagTrends, 0, 15) as $tag): ?>
                    <?php if (($tag['growth_rate_percentage'] ?? 0) > 0): ?>
                        <a href="<?php echo url('recommend?tag=' . urlencode(htmlspecialchars_decode($tag['tag']))) ?>" 
                           class="tag-item">
                            #<?php echo htmlspecialchars($tag['tag']) ?> 
                            <strong>+<?php echo round((float)$tag['growth_rate_percentage'], 1) ?>%</strong>
                        </a>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- AI洞察 -->
    <?php if (!empty($aiAnalysis->insights)): ?>
        <div class="trend-card">
            <h3 class="section-title">💡 AI洞察</h3>
            
            <?php foreach ($aiAnalysis->insights as $insight): ?>
                <div style="background: #f8fafc; padding: 12px; border-radius: 6px; border-left: 4px solid #3b82f6; margin-bottom: 8px;">
                    <?php echo htmlspecialchars($insight) ?>
                </div>
            <?php endforeach ?>
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