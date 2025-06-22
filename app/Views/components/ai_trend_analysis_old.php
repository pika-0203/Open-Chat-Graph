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
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-green-100 p-2 rounded-lg">
                    <span class="text-green-600">🚀</span>
                </div>
                現在急成長中のチャット
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <?php foreach (array_slice($risingChats, 0, 6) as $index => $chat): ?>
                    <div class="ai-card rounded-lg p-4 group">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-gradient-to-br from-green-500 to-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm">
                                <?php echo $index + 1 ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="<?php echo url('oc/' . $chat['id']) ?>" class="text-gray-900 font-medium hover:text-blue-600 transition-colors block truncate group-hover:text-blue-600">
                                    <?php echo htmlspecialchars($chat['name']) ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="growth-indicator growth-positive">
                                <span>📈</span>
                                <span>+<?php echo number_format($chat['diff_member']) ?>人</span>
                            </div>
                            <div class="text-sm text-gray-600">
                                現在<?php echo number_format($chat['member']) ?>人
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- カテゴリ別パフォーマンス -->
    <?php if (!empty($aiAnalysis->categoryInsights)): ?>
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-purple-100 p-2 rounded-lg">
                    <span class="text-purple-600">📈</span>
                </div>
                カテゴリ別パフォーマンス
            </h3>
            
            <div class="space-y-4">
                <?php foreach ($aiAnalysis->categoryInsights as $insight): ?>
                    <div class="ai-card rounded-lg p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <span class="trend-badge"><?php echo $insight['rank'] ?>位</span>
                                <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($insight['category']) ?></h4>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">平均成長率</div>
                                <div class="font-bold text-blue-600"><?php echo $insight['average_growth'] ?>人/時</div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <span class="text-sm text-gray-600">総成長数</span>
                                <div class="font-semibold text-green-600">+<?php echo number_format($insight['total_growth']) ?>人</div>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">活動チャット数</span>
                                <div class="font-semibold text-blue-600"><?php echo number_format($insight['chat_count']) ?>個</div>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-700"><?php echo htmlspecialchars($insight['description']) ?></p>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- データベースインサイト -->
    <?php if (!empty($aiAnalysis->insights)): ?>
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-indigo-100 p-2 rounded-lg">
                    <span class="text-indigo-600">🧠</span>
                </div>
                データから見えるインサイト
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <?php foreach ($aiAnalysis->insights as $insight): ?>
                    <div class="ai-card rounded-xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="bg-gradient-to-br from-blue-500 to-purple-600 text-white p-3 rounded-lg">
                                <span class="text-xl"><?php echo $insight['icon'] ?></span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 mb-2">
                                    <?php echo htmlspecialchars($insight['title']) ?>
                                </h4>
                                <p class="text-gray-700 leading-relaxed"><?php echo htmlspecialchars($insight['content']) ?></p>
                                
                                <?php if (isset($insight['confidence'])): ?>
                                    <div class="mt-3 text-xs text-blue-600">
                                        信頼度: <?php echo $insight['confidence'] ?>%
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- 時間帯パターン -->
    <?php if (!empty($aiAnalysis->timePatterns)): ?>
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-orange-100 p-2 rounded-lg">
                    <span class="text-orange-600">⏰</span>
                </div>
                時間帯分析
            </h3>
            
            <div class="ai-card rounded-xl p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold mb-3">現在の状況</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">時間帯</span>
                                <span class="font-medium"><?php echo $aiAnalysis->timePatterns['current_time_context']['time_period'] ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">活動レベル</span>
                                <span class="font-medium"><?php echo $aiAnalysis->timePatterns['current_time_context']['expected_activity_level'] ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">成長中チャット数</span>
                                <span class="font-medium text-green-600"><?php echo $aiAnalysis->timePatterns['current_time_context']['current_growth_count'] ?>個</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-3">時間帯インサイト</h4>
                        <p class="text-gray-700 mb-3"><?php echo htmlspecialchars($aiAnalysis->timePatterns['time_insights']['description']) ?></p>
                        
                        <?php if (!empty($aiAnalysis->timePatterns['time_insights']['recommended_actions'])): ?>
                            <div class="space-y-1">
                                <?php foreach ($aiAnalysis->timePatterns['time_insights']['recommended_actions'] as $action): ?>
                                    <div class="text-sm text-blue-600">💡 <?php echo htmlspecialchars($action) ?></div>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>

    <!-- 成長パターン統計 -->
    <?php if (!empty($aiAnalysis->membershipTrends)): ?>
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-teal-100 p-2 rounded-lg">
                    <span class="text-teal-600">📊</span>
                </div>
                成長統計
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="ai-card rounded-xl p-6">
                    <h4 class="font-semibold mb-4">成長分布</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">成長中</span>
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500 rounded-full" style="width: <?php echo $aiAnalysis->membershipTrends['growth_statistics']['growing_chat_percentage'] ?>%"></div>
                                </div>
                                <span class="text-sm font-medium"><?php echo $aiAnalysis->membershipTrends['growth_statistics']['growing_chat_percentage'] ?>%</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">減少中</span>
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-red-500 rounded-full" style="width: <?php echo $aiAnalysis->membershipTrends['growth_statistics']['declining_chat_percentage'] ?>%"></div>
                                </div>
                                <span class="text-sm font-medium"><?php echo $aiAnalysis->membershipTrends['growth_statistics']['declining_chat_percentage'] ?>%</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">安定</span>
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full" style="width: <?php echo $aiAnalysis->membershipTrends['growth_statistics']['stable_chat_percentage'] ?>%"></div>
                                </div>
                                <span class="text-sm font-medium"><?php echo $aiAnalysis->membershipTrends['growth_statistics']['stable_chat_percentage'] ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($aiAnalysis->membershipTrends['growth_quality'])): ?>
                    <div class="ai-card rounded-xl p-6">
                        <h4 class="font-semibold mb-4">成長の質</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">平均成長</span>
                                <span class="font-medium"><?php echo $aiAnalysis->membershipTrends['growth_quality']['average_growth'] ?>人</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">最大成長</span>
                                <span class="font-medium text-green-600"><?php echo $aiAnalysis->membershipTrends['growth_quality']['max_growth'] ?>人</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">中央値</span>
                                <span class="font-medium"><?php echo $aiAnalysis->membershipTrends['growth_quality']['median_growth'] ?>人</span>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    <?php endif ?>

    <!-- 異常検出 -->
    <?php if (!empty($aiAnalysis->anomalies)): ?>
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-red-100 p-2 rounded-lg">
                    <span class="text-red-600">⚠️</span>
                </div>
                異常パターン検出
            </h3>
            
            <div class="space-y-4">
                <?php foreach ($aiAnalysis->anomalies as $anomaly): ?>
                    <div class="ai-card border-l-4 border-orange-400 rounded-lg p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold uppercase">
                                    <?php echo $anomaly['severity'] ?>
                                </span>
                                <?php if (isset($anomaly['chat_name'])): ?>
                                    <h4 class="font-semibold text-gray-900 mt-2"><?php echo htmlspecialchars($anomaly['chat_name']) ?></h4>
                                <?php endif ?>
                            </div>
                            <?php if (isset($anomaly['growth'])): ?>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-orange-600">+<?php echo $anomaly['growth'] ?></div>
                                    <div class="text-xs text-gray-500">人の増加</div>
                                </div>
                            <?php endif ?>
                        </div>
                        
                        <p class="text-gray-800 mb-3"><?php echo htmlspecialchars($anomaly['description']) ?></p>
                        
                        <?php if (!empty($anomaly['possible_causes'])): ?>
                            <div>
                                <p class="text-sm text-gray-600 mb-2">考えられる原因:</p>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($anomaly['possible_causes'] as $cause): ?>
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                            <?php echo htmlspecialchars($cause) ?>
                                        </span>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- 実用的な予測 -->
    <?php if (!empty($aiAnalysis->predictions)): ?>
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-indigo-100 p-2 rounded-lg">
                    <span class="text-indigo-600">🔮</span>
                </div>
                今後の予測
            </h3>
            
            <div class="space-y-4">
                <?php foreach ($aiAnalysis->predictions as $prediction): ?>
                    <div class="ai-card rounded-lg p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <span class="bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full text-sm font-bold">
                                    <?php echo $prediction['timeframe'] ?>
                                </span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500">信頼度</span>
                                    <span class="text-xs font-bold text-indigo-600"><?php echo $prediction['confidence'] ?>%</span>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-gray-800 font-medium mb-2"><?php echo htmlspecialchars($prediction['prediction']) ?></p>
                        
                        <?php if (isset($prediction['reasoning'])): ?>
                            <p class="text-sm text-gray-600">根拠: <?php echo htmlspecialchars($prediction['reasoning']) ?></p>
                        <?php endif ?>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- AIコメント -->
    <?php if (!empty($aiAnalysis->aiComment)): ?>
        <div class="ai-card rounded-xl p-6">
            <div class="flex items-start gap-4">
                <div class="bg-gradient-to-br from-purple-500 to-blue-600 text-white p-3 rounded-lg">
                    <span class="text-2xl">🤖</span>
                </div>
                <div>
                    <h3 class="font-bold mb-2 text-lg">AI総評</h3>
                    <p class="text-gray-700 leading-relaxed"><?php echo htmlspecialchars($aiAnalysis->aiComment) ?></p>
                </div>
            </div>
        </div>
    <?php endif ?>
</section>
    
    @keyframes hologramShift {
        0%, 100% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
    }
    
    /* 控えめなグロー効果 */
    .ai-trend-isolated .future-glow {
        box-shadow: 
            0 0 10px rgba(59, 130, 246, 0.2),
            0 0 20px rgba(139, 92, 246, 0.1);
    }
    
    /* 控えめな3D変形効果 */
    .ai-trend-isolated .card-3d {
        transform-style: preserve-3d;
        transition: transform 0.2s ease;
    }
    
    .ai-trend-isolated .card-3d:hover {
        transform: translateY(-2px) scale(1.02);
    }

    /* データフロー用アニメーション */
    @keyframes flowRight {
        0% {
            left: -10px;
            opacity: 0;
        }
        10% {
            opacity: 0.6;
        }
        90% {
            opacity: 0.6;
        }
        100% {
            left: 100%;
            opacity: 0;
        }
    }
    
    /* レスポンシブ対応 */
    @media (max-width: 768px) {
        .ai-trend-isolated .neuro-card {
            box-shadow: 
                0 2px 4px -1px rgba(0, 0, 0, 0.1),
                0 1px 2px -1px rgba(0, 0, 0, 0.06);
        }
        
        .ai-trend-isolated .hologram-text {
            font-size: 1rem;
        }
    }
</style>

<section class="ai-trend-isolated bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-xl shadow-lg hover:shadow-xl transition-all duration-500 p-6 mb-6 relative overflow-hidden">
    <!-- 背景アニメーション -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-400 rounded-full animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-purple-400 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
    </div>
    
    <!-- ヘッダー -->
    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4 group">
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white p-3 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                <span class="text-2xl">🤖</span>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors duration-300">
                    次世代AIトレンド分析
                </h2>
                <p class="text-sm text-blue-600 font-medium">Quantum Neural Network v3.7.2</p>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-400 to-blue-500 text-white px-4 py-3 rounded-full text-sm font-bold shadow-lg">
            <span class="inline-block w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
            リアルタイム解析中
        </div>
    </div>

    <!-- AIパーソナリティコメント -->
    <?php if (!empty($aiAnalysis->aiPersonality)): ?>
        <div class="relative z-10 bg-gradient-to-r from-blue-600 to-purple-700 text-white rounded-xl p-6 mb-8 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-start gap-4">
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <span class="text-2xl">🧠</span>
                </div>
                <div>
                    <h3 class="font-bold mb-2 text-lg">AI Neural Commentary</h3>
                    <p class="leading-relaxed text-blue-50 font-medium"><?php echo htmlspecialchars($aiAnalysis->aiPersonality) ?></p>
                </div>
            </div>
        </div>
    <?php endif ?>

    <!-- リアルタイムバイブス -->
    <?php if (!empty($aiAnalysis->realTimeVibes)): ?>
        <div class="relative z-10 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-yellow-100 p-2 rounded-lg">
                    <span class="text-yellow-600"><?php echo $aiAnalysis->realTimeVibes['current']['icon'] ?? '🌟' ?></span>
                </div>
                現在のコレクティブマインド
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- 現在のバイブス -->
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-300">
                    <h4 class="font-bold text-gray-900 mb-4">🌊 今この瞬間のバイブス</h4>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl"><?php echo $aiAnalysis->realTimeVibes['current']['icon'] ?? '✨' ?></span>
                            <div>
                                <p class="font-semibold text-blue-600"><?php echo htmlspecialchars($aiAnalysis->realTimeVibes['current']['mood'] ?? '') ?></p>
                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($aiAnalysis->realTimeVibes['current']['description'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- エネルギーマップ -->
                <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-300">
                    <h4 class="font-bold text-gray-900 mb-4">⚡ コレクティブエネルギー</h4>
                    <div class="space-y-3">
                        <?php foreach ($aiAnalysis->realTimeVibes['energy_map'] ?? [] as $type => $level): ?>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 capitalize"><?php echo ucfirst(str_replace('_', ' ', $type)) ?></span>
                                <div class="flex items-center gap-2">
                                    <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-blue-500 to-purple-600 rounded-full transition-all duration-500" style="width: <?php echo $level ?>%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-blue-600"><?php echo $level ?>%</span>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>

    <!-- 感情地図 -->
    <?php if (!empty($aiAnalysis->emotionalLandscape)): ?>
        <div class="relative z-10 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-pink-100 p-2 rounded-lg">
                    <span class="text-pink-600">💭</span>
                </div>
                感情地図：コミュニティの心象風景
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <?php foreach (array_slice($aiAnalysis->emotionalLandscape, 0, 6) as $emotion): ?>
                    <div class="bg-white border border-gray-100 rounded-xl p-5 hover:shadow-lg hover:border-blue-200 transition-all duration-300 group">
                        <div class="flex items-center justify-between mb-3">
                            <a href="<?php echo url('oc/' . $emotion['id']) ?>" class="font-semibold text-gray-900 hover:text-blue-600 transition-colors truncate flex-1 mr-3 group-hover:text-blue-600">
                                <?php echo htmlspecialchars($emotion['name']) ?>
                            </a>
                            <div class="text-right">
                                <div class="text-xs text-gray-500">エネルギー</div>
                                <div class="font-bold text-blue-600"><?php echo $emotion['energy_level'] ?>%</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                <?php echo $emotion['vibe'] ?>
                            </span>
                        </div>
                        
                        <!-- 感情レーダー -->
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <?php foreach ($emotion['emotions'] as $type => $level): ?>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 capitalize"><?php echo ucfirst($type) ?></span>
                                    <div class="flex items-center gap-1">
                                        <div class="w-8 h-1 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-pink-400 to-purple-500 rounded-full" style="width: <?php echo min(100, $level) ?>%"></div>
                                        </div>
                                        <span class="text-purple-600 font-medium"><?php echo $level ?></span>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- 潜在コミュニティ発見 -->
    <?php if (!empty($aiAnalysis->hiddenCommunities)): ?>
        <div class="relative z-10 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-purple-100 p-2 rounded-lg">
                    <span class="text-purple-600">🔮</span>
                </div>
                潜在コミュニティ発見：未来の文化圏
            </h3>
            
            <div class="space-y-6">
                <?php foreach ($aiAnalysis->hiddenCommunities as $community): ?>
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-100 rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-300 group">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-purple-900 mb-2 group-hover:text-purple-600 transition-colors">
                                    <?php echo htmlspecialchars($community['name']) ?>
                                </h4>
                                <p class="text-gray-700 leading-relaxed mb-4"><?php echo htmlspecialchars($community['description']) ?></p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-lg font-bold text-purple-600"><?php echo number_format($community['members_estimated']) ?></div>
                                <div class="text-xs text-gray-600">推定メンバー</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-green-600"><?php echo $community['growth_velocity'] ?></div>
                                <div class="text-xs text-gray-600">成長速度</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-blue-600"><?php echo $community['emergence_probability'] ?>%</div>
                                <div class="text-xs text-gray-600">出現確率</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-orange-600"><?php echo $community['cultural_impact_score'] ?>/100</div>
                                <div class="text-xs text-gray-600">文化的影響</div>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($community['keywords'] as $keyword): ?>
                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-medium">
                                    #<?php echo htmlspecialchars($keyword) ?>
                                </span>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- 異常パターン検出 -->
    <?php if (!empty($aiAnalysis->anomalyDetection)): ?>
        <div class="relative z-10 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-red-100 p-2 rounded-lg">
                    <span class="text-red-600">🚨</span>
                </div>
                異常パターン検出：データの向こう側
            </h3>
            
            <div class="space-y-4">
                <?php foreach ($aiAnalysis->anomalyDetection as $anomaly): ?>
                    <div class="bg-gradient-to-r from-red-50 to-orange-50 border-l-4 border-red-400 p-5 rounded-xl shadow-md hover:shadow-lg transition-all duration-300">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold uppercase">
                                        <?php echo $anomaly['severity'] ?> Priority
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        Probability: <?php echo number_format($anomaly['probability_score'] * 100, 1) ?>%
                                    </span>
                                </div>
                                <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($anomaly['description']) ?></p>
                            </div>
                        </div>
                        
                        <?php if (!empty($anomaly['potential_causes'])): ?>
                            <div class="mt-3">
                                <p class="text-sm text-gray-600 mb-2">推定原因:</p>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($anomaly['potential_causes'] as $cause): ?>
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                            <?php echo htmlspecialchars($cause) ?>
                                        </span>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <!-- AIコメント -->
    <?php if (!empty($aiAnalysis->aiComment)): ?>
        <div class="ai-card rounded-xl p-6">
            <div class="flex items-start gap-4">
                <div class="bg-gradient-to-br from-purple-500 to-blue-600 text-white p-3 rounded-lg">
                    <span class="text-2xl">🤖</span>
                </div>
                <div>
                    <h3 class="font-bold mb-2 text-lg">AI総評</h3>
                    <p class="text-gray-700 leading-relaxed"><?php echo htmlspecialchars($aiAnalysis->aiComment) ?></p>
                </div>
            </div>
        </div>
    <?php endif ?>
</section>