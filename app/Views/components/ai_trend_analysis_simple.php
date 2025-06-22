<?php

use App\Services\AiTrend\AiTrendDataDto;

/** @var AiTrendDataDto $aiTrendData */
$risingChats = $aiTrendData->risingChats;
$categoryTrends = $aiTrendData->categoryTrends;
$tagTrends = $aiTrendData->tagTrends;
$aiAnalysis = $aiTrendData->aiAnalysis;

?>

<style>
    .ai-trend-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: #374151;
        background: #ffffff;
        max-width: 1200px;
    }
    
    .trend-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        transition: box-shadow 0.2s ease;
        background: #ffffff;
    }
    
    .trend-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .insight-item {
        padding: 16px;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 12px;
        border-left: 4px solid #3b82f6;
    }
    
    .tag-badge {
        display: inline-block;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 14px;
        margin: 2px 4px;
        text-decoration: none;
    }
    
    .growth-indicator {
        display: inline-flex;
        align-items: center;
        color: #059669;
        font-weight: 600;
        font-size: 14px;
    }
    
    .section-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 16px;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .chat-link {
        text-decoration: none;
        color: #1f2937;
        font-weight: 600;
    }
    
    .chat-link:hover {
        color: #3b82f6;
    }
    
    .grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 16px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    
    .stat-card {
        text-align: center;
        padding: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #ffffff;
    }
    
    .stat-number {
        font-size: 24px;
        font-weight: 700;
        color: #3b82f6;
    }
    
    .stat-label {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }
</style>

<section class="ai-trend-container">
    <!-- ヘッダー -->
    <div class="trend-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="font-size: 32px;">🤖</div>
            <div>
                <h2 style="font-size: 24px; font-weight: 700; margin: 0;">AIトレンド分析</h2>
                <p style="margin: 4px 0 0 0; opacity: 0.9;">データに基づくリアルタイム洞察</p>
            </div>
        </div>
    </div>

    <!-- AI洞察サマリー -->
    <?php if (!empty($aiAnalysis->summary)): ?>
        <div class="trend-card">
            <h3 class="section-title">
                <span>💡</span>
                AI分析サマリー
            </h3>
            <div class="insight-item">
                <p style="margin: 0; line-height: 1.6;"><?php echo htmlspecialchars($aiAnalysis->summary) ?></p>
            </div>
        </div>
    <?php endif ?>

    <!-- 重要インサイト -->
    <?php if (!empty($aiAnalysis->insights)): ?>
        <div class="trend-card">
            <h3 class="section-title">
                <span>🎯</span>
                重要な発見
            </h3>
            <?php foreach (array_slice($aiAnalysis->insights, 0, 3) as $insight): ?>
                <div class="insight-item">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <span style="font-size: 24px;"><?php echo $insight['icon'] ?? '📊' ?></span>
                        <div style="flex: 1;">
                            <h4 style="font-weight: 600; margin: 0 0 8px 0; color: #1f2937;">
                                <?php echo htmlspecialchars($insight['title']) ?>
                            </h4>
                            <p style="margin: 0; color: #4b5563; line-height: 1.5;">
                                <?php echo htmlspecialchars($insight['content']) ?>
                            </p>
                            <?php if (isset($insight['confidence'])): ?>
                                <div style="margin-top: 8px; font-size: 12px; color: #6b7280;">
                                    信頼度: <strong><?php echo $insight['confidence'] ?>%</strong>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <!-- 成長中チャット -->
    <?php if (!empty($risingChats)): ?>
        <div class="trend-card">
            <h3 class="section-title">
                <span>🚀</span>
                急成長中のチャットルーム
            </h3>
            <div class="grid-2">
                <?php foreach (array_slice($risingChats, 0, 6) as $index => $chat): ?>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px;">
                        <div style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                            <?php echo $index + 1 ?>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <a href="<?php echo url('oc/' . $chat['id']) ?>" class="chat-link">
                                <?php echo htmlspecialchars($chat['name']) ?>
                            </a>
                            <div class="growth-indicator">
                                <span>↗</span>
                                +<?php echo number_format($chat['diff_member']) ?>人
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- カテゴリ動向 -->
    <?php if (!empty($categoryTrends)): ?>
        <div class="trend-card">
            <h3 class="section-title">
                <span>📈</span>
                カテゴリ別成長動向
            </h3>
            <?php foreach (array_slice($categoryTrends, 0, 5) as $trend): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #f3f4f6;">
                    <div>
                        <div style="font-weight: 600; color: #1f2937;">
                            <?php echo htmlspecialchars($trend['category_name'] ?? 'その他') ?>
                        </div>
                        <div style="font-size: 14px; color: #6b7280;">
                            <?php echo $trend['chat_count'] ?>個のチャット
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div class="growth-indicator">
                            +<?php echo number_format($trend['total_growth']) ?>人
                        </div>
                        <div style="font-size: 12px; color: #6b7280;">
                            平均 <?php echo number_format($trend['avg_growth'], 1) ?>人/時
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <!-- 注目タグ -->
    <?php if (!empty($tagTrends)): ?>
        <div class="trend-card">
            <h3 class="section-title">
                <span>🏷️</span>
                注目のキーワード
            </h3>
            <div style="display: flex; flex-wrap: gap: 8px;">
                <?php foreach (array_slice($tagTrends, 0, 15) as $tag): ?>
                    <?php if (($tag['total_1h_growth'] ?? 0) > 0): ?>
                        <span class="tag-badge">
                            #<?php echo htmlspecialchars($tag['tag']) ?>
                            <span style="color: #059669; font-weight: 600;">
                                +<?php echo $tag['total_1h_growth'] ?>
                            </span>
                        </span>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- 予測・推奨 -->
    <?php if (!empty($aiAnalysis->recommendations)): ?>
        <div class="trend-card">
            <h3 class="section-title">
                <span>💡</span>
                AIからの提案
            </h3>
            <?php foreach (array_slice($aiAnalysis->recommendations, 0, 3) as $rec): ?>
                <div class="insight-item">
                    <h4 style="font-weight: 600; margin: 0 0 8px 0; color: #1f2937;">
                        <?php echo htmlspecialchars($rec['title']) ?>
                    </h4>
                    <p style="margin: 0; color: #4b5563; line-height: 1.5;">
                        <?php echo htmlspecialchars($rec['description']) ?>
                    </p>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</section>
