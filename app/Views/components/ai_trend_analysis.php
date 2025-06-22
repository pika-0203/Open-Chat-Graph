<?php

use App\Services\AiTrend\AiTrendDataDto;

/** @var AiTrendDataDto $aiTrendData */
$risingChats = $aiTrendData->risingChats;
$categoryTrends = $aiTrendData->categoryTrends;
$tagTrends = $aiTrendData->tagTrends;
$aiAnalysis = $aiTrendData->aiAnalysis;
$realtimeMetrics = $aiTrendData->realtimeMetrics;

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
    
    .trend-subtitle {
        opacity: 0.9;
        margin: 0;
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
    
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .metric-item {
        background: #f8fafc;
        padding: 16px;
        border-radius: 6px;
        text-align: center;
    }
    
    .metric-value {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 4px;
    }
    
    .metric-label {
        font-size: 14px;
        color: #6b7280;
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
    
    .category-list {
        space-y: 8px;
    }
    
    .category-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        background: #f9fafb;
        border-radius: 6px;
        margin-bottom: 8px;
    }
    
    .category-name {
        font-weight: 600;
        color: #1f2937;
    }
    
    .category-growth {
        color: #059669;
        font-weight: 600;
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
    }
    
    .summary-text {
        background: #f8fafc;
        padding: 16px;
        border-radius: 6px;
        border-left: 4px solid #3b82f6;
        line-height: 1.6;
        color: #374151;
        margin: 0;
    }
</style>

<section class="trend-container">
    <!-- ヘッダー -->
    <div class="trend-header">
        <h2 class="trend-title">📊 トレンド分析</h2>
        <p class="trend-subtitle">リアルタイムの成長動向</p>
    </div>

    <!-- リアルタイムメトリクス -->
    <?php if (!empty($realtimeMetrics)): ?>
        <div class="trend-card">
            <h3 class="section-title">📈 現在の状況</h3>
            <div class="metrics-grid">
                <div class="metric-item">
                    <div class="metric-value"><?php echo number_format($realtimeMetrics['current_hour_growth'] ?? 0) ?></div>
                    <div class="metric-label">現在の成長数</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value"><?php echo $realtimeMetrics['high_growth_count'] ?? 0 ?></div>
                    <div class="metric-label">急成長チャット</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value"><?php echo $realtimeMetrics['new_chats_count'] ?? 0 ?></div>
                    <div class="metric-label">新規チャット</div>
                </div>
            </div>
        </div>
    <?php endif ?>

    <!-- 分析サマリー -->
    <?php if (!empty($aiAnalysis->summary)): ?>
        <div class="trend-card">
            <h3 class="section-title">💡 今の動向</h3>
            <p class="summary-text"><?php echo htmlspecialchars($aiAnalysis->summary) ?></p>
        </div>
    <?php endif ?>

    <!-- 成長中チャット -->
    <?php if (!empty($risingChats)): ?>
        <div class="trend-card">
            <h3 class="section-title">🚀 注目のチャット</h3>
            <div class="chat-list">
                <?php foreach (array_slice($risingChats, 0, 6) as $index => $chat): ?>
                    <div class="chat-item">
                        <div class="chat-rank"><?php echo $index + 1 ?></div>
                        <div class="chat-info">
                            <a href="<?php echo url('oc/' . $chat['id']) ?>" class="chat-name">
                                <?php echo htmlspecialchars($chat['name']) ?>
                            </a>
                            <div class="chat-growth">+<?php echo number_format($chat['diff_member']) ?>人</div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- カテゴリ動向 -->
    <?php if (!empty($categoryTrends)): ?>
        <div class="trend-card">
            <h3 class="section-title">📊 カテゴリ別動向</h3>
            <div class="category-list">
                <?php foreach (array_slice($categoryTrends, 0, 5) as $trend): ?>
                    <div class="category-item">
                        <div>
                            <div class="category-name"><?php echo htmlspecialchars($trend['category_name'] ?? 'その他') ?></div>
                            <div style="font-size: 14px; color: #6b7280;"><?php echo $trend['chat_count'] ?>個のチャット</div>
                        </div>
                        <div class="category-growth">+<?php echo number_format($trend['total_growth']) ?>人</div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- 重要な発見 -->
    <?php if (!empty($aiAnalysis->insights)): ?>
        <div class="trend-card">
            <h3 class="section-title">🔍 深層分析</h3>
            <?php foreach (array_slice($aiAnalysis->insights, 0, 2) as $insight): ?>
                <div style="background: #f8fafc; padding: 16px; border-radius: 6px; border-left: 4px solid #3b82f6; margin-bottom: 12px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <span style="font-size: 24px;"><?php echo $insight['icon'] ?? '🔍' ?></span>
                        <div style="flex: 1;">
                            <h4 style="font-weight: 600; margin: 0 0 8px 0; color: #1f2937;">
                                <?php echo htmlspecialchars($insight['title']) ?>
                            </h4>
                            <p style="margin: 0; color: #4b5563; line-height: 1.5;">
                                <?php echo htmlspecialchars($insight['content']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <!-- 注目タグ -->
    <?php if (!empty($tagTrends)): ?>
        <div class="trend-card">
            <h3 class="section-title">🏷️ 人気キーワード</h3>
            <div class="tag-list">
                <?php foreach (array_slice($tagTrends, 0, 10) as $tag): ?>
                    <?php if (($tag['total_1h_growth'] ?? 0) > 0): ?>
                        <span class="tag-item">
                            #<?php echo htmlspecialchars($tag['tag']) ?> +<?php echo $tag['total_1h_growth'] ?>
                        </span>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>
</section>