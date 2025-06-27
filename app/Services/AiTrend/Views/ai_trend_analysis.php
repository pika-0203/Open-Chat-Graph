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
        gap: 16px;
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
                AIがデータに基づき、今後成長が期待されると判断したオープンチャットです。
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
                                (<?php echo htmlspecialchars(AiTrendInsightHelper::getCategoryName($chat['category'] ?? null)) ?>)
                            </div>
                            
                            <!-- AI選出理由 -->
                            <?php if (!empty($chat['selection_rationale'])): ?>
                                <div class="ai-insight">
                                    🎯 <strong>AI選出理由:</strong> <?php echo htmlspecialchars($chat['selection_rationale']) ?>
                                </div>
                            <?php else: ?>
                                <div class="ai-insight">
                                    🔥 <?php echo AiTrendInsightHelper::generateInsightText($chat) ?>
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
                <?php foreach (array_slice($tagTrends, 0, 15) as $index => $tag): ?>
                        <div style="position: relative; display: inline-block; <?php echo $index < 3 ? 'margin-bottom: 20px;' : '' ?>">
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
                            
                            <!-- 上位3つのタグに選出理由を表示 -->
                            <?php if ($index < 3 && !empty($tag['ai_rationale'])): ?>
                                <div class="ai-insight" style="margin-top: 8px; position: static; width: auto; max-width: 300px; display: block; font-size: 12px; padding: 8px; background: #f0f9ff; border-left: 3px solid #3b82f6; border-radius: 4px; line-height: 1.4;">
                                    🎯 <strong>選出理由:</strong> <?php echo htmlspecialchars($tag['ai_rationale']) ?>
                                </div>
                            <?php elseif (!empty($tag['ai_rationale'])): ?>
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