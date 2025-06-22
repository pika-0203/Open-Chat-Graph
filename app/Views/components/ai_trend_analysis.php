<?php

use App\Services\AiTrend\AiTrendDataDto;

/** @var AiTrendDataDto $aiTrendData */
$risingChats = $aiTrendData->risingChats;
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
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
    }
    
    .category-item {
        display: grid;
        grid-template-columns: 2fr 1fr;
        align-items: center;
        gap: 16px;
        padding: 16px;
        background: #f9fafb;
        border-radius: 8px;
        border-left: 4px solid #e5e7eb;
        transition: all 0.2s ease;
    }
    
    .category-item:hover {
        background: #f3f4f6;
        border-left-color: #667eea;
        transform: translateX(2px);
    }
    
    .category-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 16px;
    }
    
    .category-growth {
        color: #059669;
        font-weight: 700;
        font-size: 18px;
        text-align: right;
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


    <!-- 重要な動向（統合版） -->
    <div class="trend-card">
        <h3 class="section-title">🚨 重要な動向</h3>
        
        <?php if (!empty($aiAnalysis->alerts)): ?>
            <?php foreach ($aiAnalysis->alerts as $alert): ?>
                <div style="background: <?php echo $alert['level'] === 'critical' ? '#fef2f2' : ($alert['level'] === 'warning' ? '#fefbf2' : '#f0f9ff') ?>; 
                           border: 1px solid <?php echo $alert['level'] === 'critical' ? '#fecaca' : ($alert['level'] === 'warning' ? '#fed7aa' : '#bae6fd') ?>; 
                           border-radius: 6px; padding: 16px; margin-bottom: 12px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <span style="font-size: 20px;"><?php echo $alert['icon'] ?></span>
                        <div style="flex: 1;">
                            <h4 style="font-weight: 600; margin: 0 0 8px 0; color: #1f2937;">
                                <?php echo htmlspecialchars($alert['title']) ?>
                            </h4>
                            <p style="margin: 0; color: #4b5563; line-height: 1.5;">
                                <?php echo htmlspecialchars($alert['message']) ?>
                            </p>
                            <div style="font-size: 12px; color: #6b7280; margin-top: 8px;">
                                <?php echo $alert['timestamp'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        <?php endif ?>

        <?php if (!empty($aiAnalysis->insights)): ?>
            <?php foreach (array_slice($aiAnalysis->insights, 0, 5) as $insight): ?>
                <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 6px; padding: 16px; margin-bottom: 12px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <span style="font-size: 20px;"><?php echo $insight['icon'] ?? '💡' ?></span>
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
        <?php endif ?>

        <?php if (!empty($aiAnalysis->summary)): ?>
            <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px; border-left: 4px solid #3b82f6;">
                <h4 style="font-weight: 600; margin: 0 0 8px 0; color: #1f2937; display: flex; align-items: center; gap: 8px;">
                    <span>💡</span> 分析サマリー
                </h4>
                <p style="margin: 0; color: #4b5563; line-height: 1.6;"><?php echo htmlspecialchars($aiAnalysis->summary) ?></p>
            </div>
        <?php endif ?>
    </div>




    <!-- テーマ推奨（LLM分析結果） -->
    <?php if (!empty($aiAnalysis->recommendations)): ?>
        <div class="trend-card">
            <h3 class="section-title">🎯 おすすめテーマ（AI分析）</h3>
            <div style="margin-bottom: 16px; padding: 12px; background: #fef7ff; border-radius: 6px; font-size: 14px; color: #7c2d8e;">
                🤖 実データ分析に基づく、今最も集客力の高いテーマをAIが厳選
            </div>
            
            <?php foreach (array_slice($aiAnalysis->recommendations, 0, 5) as $rec): ?>
                <div style="background: #fafafa; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <h4 style="font-weight: 700; color: #1f2937; margin: 0; font-size: 16px;">
                            <?php echo htmlspecialchars($rec['theme']) ?>
                        </h4>
                        <div style="display: flex; gap: 8px;">
                            <span style="background: <?php echo $rec['competition'] === '低' ? '#dcfce7' : ($rec['competition'] === '中' ? '#fef3c7' : '#fecaca') ?>; 
                                        color: <?php echo $rec['competition'] === '低' ? '#166534' : ($rec['competition'] === '中' ? '#92400e' : '#dc2626') ?>; 
                                        padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                競争<?php echo $rec['competition'] ?>
                            </span>
                            <span style="background: <?php echo $rec['growth_potential'] === '高' ? '#dcfce7' : ($rec['growth_potential'] === '中' ? '#fef3c7' : '#f3f4f6') ?>; 
                                        color: <?php echo $rec['growth_potential'] === '高' ? '#166534' : ($rec['growth_potential'] === '中' ? '#92400e' : '#6b7280') ?>; 
                                        padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                成長性<?php echo $rec['growth_potential'] ?>
                            </span>
                        </div>
                    </div>
                    
                    <div style="color: #4b5563; font-size: 14px; line-height: 1.5; margin-bottom: 8px;">
                        <strong>なぜ今狙い目？</strong> <?php echo htmlspecialchars($rec['reason']) ?>
                    </div>
                    
                    <div style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">
                        <strong>ターゲット:</strong> <?php echo htmlspecialchars($rec['target']) ?>
                    </div>
                    
                    <div style="background: #f8fafc; padding: 10px; border-radius: 6px; border-left: 3px solid #3b82f6; font-size: 13px; color: #374151;">
                        <strong>運営戦略:</strong> <?php echo htmlspecialchars($rec['strategy']) ?>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <!-- 注目タグ -->
    <?php if (!empty($tagTrends)): ?>
        <div class="trend-card">
            <h3 class="section-title">🏷️ 今狙い目のキーワード</h3>
            <div style="margin-bottom: 16px; padding: 12px; background: #f0f9ff; border-radius: 6px; font-size: 14px; color: #1e40af;">
                📈 1週間前と比較した成長率でランキング！伸び率の高いキーワードを狙おう
            </div>
            <div class="tag-list">
                <?php foreach (array_slice($tagTrends, 0, 12) as $tag): ?>
                    <?php if (($tag['growth_rate_percentage'] ?? 0) > 0): ?>
                        <?php 
                        $tagName = $tag['tag'];
                        $growthRate = $tag['growth_rate_percentage'] ?? 0;
                        $marketSize = '';
                        $recommendation = '';
                        
                        // 成長率による分類
                        if ($growthRate >= 20) {
                            $marketSize = '急伸中';
                            $recommendation = '今すぐ参入チャンス';
                        } elseif ($growthRate >= 10) {
                            $marketSize = '高成長';
                            $recommendation = '成長トレンドに乗るチャンス';
                        } elseif ($growthRate >= 5) {
                            $marketSize = '成長中';
                            $recommendation = '安定成長が期待できる';
                        } else {
                            $marketSize = '微増';
                            $recommendation = '安定分野';
                        }
                        
                        // タグ別の特殊分析
                        if ($tagName === 'なりきり') {
                            $marketSize = '大市場';
                            $recommendation = '競争激しいが需要巨大';
                        } elseif (stripos($tagName, 'Stray Kids') !== false || stripos($tagName, 'スキズ') !== false) {
                            $marketSize = '韓流ブーム';
                            $recommendation = 'K-POP人気継続中';
                        }
                        ?>
                        <a href="<?php echo url('recommend?tag=' . urlencode(htmlspecialchars_decode($tagName))) ?>" 
                           class="tag-item" style="position: relative;" 
                           title="<?php echo $recommendation ?> (<?php echo $tag['room_count'] ?>チャット, 週間成長率<?php echo $growthRate ?>%)">
                            #<?php echo htmlspecialchars($tagName) ?> 
                            <strong><?php echo $growthRate ?>%</strong>
                            <small style="opacity: 0.8; font-size: 10px; margin-left: 4px;"><?php echo $marketSize ?></small>
                        </a>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
            <div style="margin-top: 12px; padding: 12px; background: #ecfdf5; border-radius: 6px; border-left: 4px solid #10b981;">
                <div style="font-size: 14px; font-weight: 600; color: #047857; margin-bottom: 4px;">キーワード戦略</div>
                <div style="font-size: 13px; color: #065f46; line-height: 1.4;">
                    • <strong>大市場キーワード</strong>：競争激しいが認知度高い<br>
                    • <strong>急成長キーワード</strong>：トレンドの波に乗るチャンス<br>
                    • <strong>ニッチキーワード</strong>：競争少なく確実に集客可能
                </div>
            </div>
        </div>
    <?php endif ?>
</section>