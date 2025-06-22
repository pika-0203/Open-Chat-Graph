<?php

/** @var array $aiTrendData */
$risingChats = $aiTrendData['risingChats'] ?? [];
$categoryTrends = $aiTrendData['categoryTrends'] ?? [];
$tagTrends = $aiTrendData['tagTrends'] ?? [];
$aiAnalysis = $aiTrendData['aiAnalysis'] ?? [];

?>

<section class="ai-trend-analysis">
    <div class="ranking-title-wrap">
        <h2 class="ranking-title-name">AIトレンド分析</h2>
        <span class="ranking-title-emoji">🤖</span>
        <div class="ranking-update-time">
            <span class="update-text">リアルタイム分析</span>
        </div>
    </div>

    <?php if (!empty($aiAnalysis['summary'])): ?>
        <div class="ai-summary">
            <div class="ai-summary-content">
                <span class="ai-summary-icon">💡</span>
                <p class="ai-summary-text"><?php echo htmlspecialchars($aiAnalysis['summary']) ?></p>
            </div>
        </div>
    <?php endif ?>

    <?php if (!empty($aiAnalysis['insights'])): ?>
        <div class="ai-insights">
            <h3 class="ai-section-title">📊 分析結果</h3>
            <div class="ai-insights-grid">
                <?php foreach ($aiAnalysis['insights'] as $insight): ?>
                    <div class="ai-insight-item">
                        <span class="ai-insight-icon"><?php echo $insight['icon'] ?></span>
                        <div class="ai-insight-content">
                            <h4 class="ai-insight-title"><?php echo htmlspecialchars($insight['title']) ?></h4>
                            <p class="ai-insight-text"><?php echo htmlspecialchars($insight['content']) ?></p>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <?php if (!empty($risingChats)): ?>
        <div class="ai-trending-chats">
            <h3 class="ai-section-title">🚀 急成長チャット</h3>
            <div class="trending-chats-list">
                <?php foreach (array_slice($risingChats, 0, 5) as $index => $chat): ?>
                    <div class="trending-chat-item">
                        <span class="trending-rank"><?php echo $index + 1 ?></span>
                        <div class="trending-chat-info">
                            <a href="<?php echo url('oc/' . $chat['id']) ?>" class="trending-chat-name">
                                <?php echo htmlspecialchars($chat['name']) ?>
                            </a>
                            <span class="trending-chat-growth">+<?php echo number_format($chat['diff_member']) ?>人</span>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <?php if (!empty($tagTrends)): ?>
        <div class="ai-tag-trends">
            <h3 class="ai-section-title">🏷️ トレンドタグ</h3>
            <div class="tag-trends-cloud">
                <?php foreach (array_slice($tagTrends, 0, 12) as $tag): ?>
                    <a href="<?php echo url('recommend?tag=' . urlencode($tag['tag'])) ?>" class="trend-tag">
                        #<?php echo htmlspecialchars($tag['tag']) ?>
                    </a>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <?php if (!empty($aiAnalysis['predictions'])): ?>
        <div class="ai-predictions">
            <h3 class="ai-section-title">🔮 予測</h3>
            <div class="predictions-list">
                <?php foreach ($aiAnalysis['predictions'] as $prediction): ?>
                    <div class="prediction-item">
                        <span class="prediction-confidence <?php echo $prediction['confidence'] ?>"></span>
                        <p class="prediction-text"><?php echo htmlspecialchars($prediction['content']) ?></p>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>
</section>

<style>
.ai-trend-analysis {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 1.5rem;
    margin: 1rem 0;
    color: white;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
}

.ai-trend-analysis .ranking-title-wrap {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.ai-trend-analysis .ranking-title-name {
    color: white;
    margin: 0;
    font-size: 1.3rem;
    font-weight: bold;
}

.ai-trend-analysis .ranking-title-emoji {
    font-size: 1.5rem;
    margin-left: 0.5rem;
}

.ai-trend-analysis .ranking-update-time {
    margin-left: auto;
}

.ai-trend-analysis .update-text {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
}

.ai-summary {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    backdrop-filter: blur(10px);
}

.ai-summary-content {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}

.ai-summary-icon {
    font-size: 1.2rem;
    margin-top: 0.1rem;
}

.ai-summary-text {
    margin: 0;
    line-height: 1.6;
    font-size: 0.95rem;
}

.ai-section-title {
    color: white;
    font-size: 1.1rem;
    margin: 1.5rem 0 1rem 0;
    font-weight: bold;
}

.ai-insights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
}

.ai-insight-item {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    backdrop-filter: blur(10px);
}

.ai-insight-icon {
    font-size: 1.3rem;
    margin-top: 0.1rem;
}

.ai-insight-title {
    color: white;
    font-size: 0.9rem;
    margin: 0 0 0.5rem 0;
    font-weight: bold;
}

.ai-insight-text {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.85rem;
    margin: 0;
    line-height: 1.5;
}

.trending-chats-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.trending-chat-item {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    backdrop-filter: blur(10px);
}

.trending-rank {
    background: rgba(255, 255, 255, 0.2);
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: bold;
}

.trending-chat-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    flex: 1;
}

.trending-chat-name {
    color: white;
    font-size: 0.9rem;
    text-decoration: none;
    font-weight: 500;
}

.trending-chat-name:hover {
    text-decoration: underline;
}

.trending-chat-growth {
    color: #4ade80;
    font-size: 0.8rem;
    font-weight: bold;
}

.tag-trends-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.trend-tag {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    backdrop-filter: blur(10px);
    transition: all 0.2s ease;
}

.trend-tag:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-1px);
}

.predictions-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.prediction-item {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    backdrop-filter: blur(10px);
}

.prediction-confidence {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-top: 0.5rem;
    flex-shrink: 0;
}

.prediction-confidence.high {
    background: #4ade80;
}

.prediction-confidence.medium {
    background: #fbbf24;
}

.prediction-confidence.low {
    background: #f87171;
}

.prediction-text {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.85rem;
    margin: 0;
    line-height: 1.5;
}

/* モバイル対応 */
@media (max-width: 768px) {
    .ai-trend-analysis {
        padding: 1rem;
        margin: 0.75rem 0;
    }
    
    .ai-insights-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .trending-chat-item {
        padding: 0.5rem 0.75rem;
    }
    
    .trend-tag {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
    }
}
</style>
