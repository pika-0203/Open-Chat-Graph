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

<script>
function toggleStrategy(button) {
    const content = button.closest('.strategy-content');
    const preview = content.querySelector('.strategy-preview');
    const full = content.querySelector('.strategy-full');
    
    if (full.style.display === 'none') {
        preview.style.display = 'none';
        full.style.display = 'block';
        button.textContent = '折りたたむ';
    } else {
        preview.style.display = 'block';
        full.style.display = 'none';
        button.textContent = '展開';
    }
}
</script>

<section class="trend-container">
    <!-- ヘッダー -->
    <div class="trend-header">
        <h2 class="trend-title">📊 トレンド分析</h2>
        <p class="trend-subtitle">リアルタイムの成長動向</p>
    </div>


    <!-- 重要な動向（統合版・5件制限） -->
    <div class="trend-card">
        <h3 class="section-title">🚨 重要な動向</h3>
        
        <?php 
        // alertsとinsightsを統合して最大5件まで表示
        $allImportantItems = [];
        
        // alertsを追加（timestamp付き）
        if (!empty($aiAnalysis->alerts)) {
            foreach ($aiAnalysis->alerts as $alert) {
                $alert['timestamp'] = date('Y-m-d H:i:s');
                $allImportantItems[] = $alert;
            }
        }
        
        // 5件に足りない分だけinsightsから追加
        $remainingSlots = 5 - count($allImportantItems);
        if ($remainingSlots > 0 && !empty($aiAnalysis->insights)) {
            $selectedInsights = array_slice($aiAnalysis->insights, 0, $remainingSlots);
            foreach ($selectedInsights as $insight) {
                // insightsをalerts形式に変換
                $allImportantItems[] = [
                    'level' => 'info',
                    'icon' => $insight['icon'] ?? '💡',
                    'title' => $insight['title'],
                    'message' => $insight['content'],
                    'action_required' => false,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'related_chats' => $insight['related_chats'] ?? []
                ];
            }
        }
        ?>
        
        <?php foreach (array_slice($allImportantItems, 0, 5) as $item): ?>
            <div style="background: <?php echo $item['level'] === 'critical' ? '#fef2f2' : ($item['level'] === 'warning' ? '#fefbf2' : '#f0f9ff') ?>; 
                       border: 1px solid <?php echo $item['level'] === 'critical' ? '#fecaca' : ($item['level'] === 'warning' ? '#fed7aa' : '#bae6fd') ?>; 
                       border-radius: 6px; padding: 16px; margin-bottom: 12px;">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <span style="font-size: 20px;"><?php echo $item['icon'] ?></span>
                    <div style="flex: 1;">
                        <h4 style="font-weight: 600; margin: 0 0 8px 0; color: #1f2937;">
                            <?php echo htmlspecialchars($item['title']) ?>
                        </h4>
                        <p style="margin: 0; color: #4b5563; line-height: 1.5;">
                            <?php echo htmlspecialchars($item['message']) ?>
                        </p>
                        <?php if (!empty($item['related_chats'])): ?>
                            <div style="margin-top: 8px;">
                                <span style="font-size: 12px; color: #6b7280;">関連チャット:</span>
                                <?php foreach ($item['related_chats'] as $chatId): ?>
                                    <?php if ($chatId && $chatId !== 'null'): ?>
                                        <a href="<?php echo url('/oc/' . $chatId) ?>" 
                                           style="font-size: 11px; color: #3b82f6; text-decoration: none; margin-left: 4px; padding: 1px 4px; background: #eff6ff; border-radius: 3px;">
                                            #<?php echo $chatId ?>
                                        </a>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                        <div style="font-size: 12px; color: #6b7280; margin-top: 8px;">
                            <?php echo $item['timestamp'] ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>

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
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <strong>運営戦略:</strong>
                            <button onclick="toggleStrategy(this)" style="background: none; border: 1px solid #d1d5db; border-radius: 4px; padding: 2px 6px; font-size: 11px; cursor: pointer; color: #6b7280;">
                                展開
                            </button>
                        </div>
                        <div class="strategy-content" style="margin-top: 6px;">
                            <div class="strategy-preview">
                                <?php echo htmlspecialchars(mb_substr($rec['strategy'], 0, 60)) ?>...
                            </div>
                            <div class="strategy-full" style="display: none;">
                                <?php echo htmlspecialchars($rec['strategy']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($rec['example_chats'])): ?>
                        <div style="margin-top: 8px; padding: 8px; background: #f0f9ff; border-radius: 4px; border-left: 3px solid #60a5fa;">
                            <div style="font-size: 12px; color: #1e40af; font-weight: 600; margin-bottom: 4px;">
                                📊 成功事例チャット:
                            </div>
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                <?php foreach ($rec['example_chats'] as $chatId): ?>
                                    <?php if ($chatId && $chatId !== 'null'): ?>
                                        <a href="<?php echo url('/oc/' . $chatId) ?>" 
                                           style="font-size: 11px; color: #1e40af; text-decoration: none; padding: 2px 6px; background: #dbeafe; border-radius: 4px; display: inline-block;">
                                            チャット #<?php echo $chatId ?>
                                        </a>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </div>
                        </div>
                    <?php endif ?>
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