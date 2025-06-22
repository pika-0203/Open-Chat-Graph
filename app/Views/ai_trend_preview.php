<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_meta->title ?></title>
    <meta name="description" content="<?php echo $_meta->description ?>">
    <link rel="stylesheet" href="<?php echo fileUrl('/style/mvp.css') ?>">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .ai-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .ai-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .ai-icon {
            font-size: 48px;
            margin-right: 20px;
        }
        .ai-title {
            flex: 1;
        }
        .ai-title h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .ai-subtitle {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .ai-summary {
            background: rgba(255,255,255,0.15);
            padding: 20px;
            border-radius: 12px;
            font-size: 18px;
            line-height: 1.6;
            backdrop-filter: blur(10px);
        }
        .insights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .insight-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        .insight-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .insight-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .insight-icon {
            font-size: 32px;
            margin-right: 15px;
        }
        .insight-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        .insight-content {
            color: #666;
            line-height: 1.6;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .trending-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .trending-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #333;
        }
        .chat-list {
            display: grid;
            gap: 15px;
        }
        .chat-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .chat-item:hover {
            background: #e9ecef;
        }
        .chat-img {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            margin-right: 15px;
            object-fit: cover;
        }
        .chat-info {
            flex: 1;
        }
        .chat-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .chat-desc {
            color: #666;
            font-size: 14px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .chat-growth {
            text-align: right;
        }
        .growth-value {
            font-size: 24px;
            font-weight: 700;
            color: #28a745;
        }
        .growth-label {
            font-size: 12px;
            color: #666;
        }
        .tag-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }
        .tag-item {
            background: #e9ecef;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            color: #495057;
            transition: all 0.2s;
        }
        .tag-item:hover {
            background: #667eea;
            color: white;
            transform: scale(1.05);
        }
        .prediction-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        .prediction-content {
            color: #495057;
            line-height: 1.6;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">← トップページに戻る</a>
        
        <!-- AIトレンド分析セクション -->
        <div class="ai-section">
            <div class="ai-header">
                <div class="ai-icon">🤖</div>
                <div class="ai-title">
                    <h1>AIトレンド分析</h1>
                    <p class="ai-subtitle">リアルタイムデータから見る今のトレンド</p>
                </div>
            </div>
            
            <div class="ai-summary">
                <?php echo htmlspecialchars($aiAnalysis['summary']) ?>
            </div>
        </div>

        <!-- 統計情報 -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($overallStats['total_chats']) ?></div>
                <div class="stat-label">総チャット数</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($overallStats['total_members']) ?></div>
                <div class="stat-label">総メンバー数</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">+<?php echo number_format($overallStats['total_growth']) ?></div>
                <div class="stat-label">1時間の増加数</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($overallStats['growing_chats']) ?></div>
                <div class="stat-label">成長中のチャット</div>
            </div>
        </div>

        <!-- AIインサイト -->
        <div class="insights-grid">
            <?php foreach ($aiAnalysis['insights'] as $insight): ?>
            <div class="insight-card">
                <div class="insight-header">
                    <div class="insight-icon"><?php echo $insight['icon'] ?></div>
                    <div class="insight-title"><?php echo htmlspecialchars($insight['title']) ?></div>
                </div>
                <div class="insight-content">
                    <?php echo htmlspecialchars($insight['content']) ?>
                </div>
            </div>
            <?php endforeach ?>
        </div>

        <!-- 急上昇チャット -->
        <div class="trending-section">
            <h2 class="trending-title">🚀 急上昇中のオープンチャット</h2>
            <div class="chat-list">
                <?php foreach (array_slice($risingChats, 0, 5) as $chat): ?>
                <div class="chat-item">
                    <img src="<?php echo htmlspecialchars($chat['img_url']) ?>" alt="" class="chat-img">
                    <div class="chat-info">
                        <div class="chat-name"><?php echo htmlspecialchars($chat['name']) ?></div>
                        <div class="chat-desc"><?php echo htmlspecialchars($chat['description']) ?></div>
                    </div>
                    <div class="chat-growth">
                        <div class="growth-value">+<?php echo number_format($chat['diff_member']) ?></div>
                        <div class="growth-label">1時間で増加</div>
                    </div>
                </div>
                <?php endforeach ?>
            </div>
        </div>

        <!-- カテゴリトレンド -->
        <div class="trending-section">
            <h2 class="trending-title">📊 カテゴリ別トレンド</h2>
            <div class="chat-list">
                <?php foreach (array_slice($categoryTrends, 0, 5) as $trend): ?>
                <div class="chat-item">
                    <div class="chat-info">
                        <div class="chat-name"><?php echo htmlspecialchars($trend['category_name']) ?></div>
                        <div class="chat-desc"><?php echo number_format($trend['chat_count']) ?>個のチャット</div>
                    </div>
                    <div class="chat-growth">
                        <div class="growth-value"><?php echo $trend['total_growth'] > 0 ? '+' : '' ?><?php echo number_format($trend['total_growth']) ?></div>
                        <div class="growth-label">1時間の合計増減</div>
                    </div>
                </div>
                <?php endforeach ?>
            </div>
        </div>

        <!-- トレンドタグ -->
        <div class="trending-section">
            <h2 class="trending-title">🏷️ 注目のタグ</h2>
            <div class="tag-cloud">
                <?php foreach (array_slice($tagTrends, 0, 20) as $tag): ?>
                <div class="tag-item">
                    <?php echo htmlspecialchars($tag['tag']) ?> 
                    <span style="color: #28a745; font-weight: 600;">+<?php echo number_format($tag['total_1h_growth']) ?></span>
                </div>
                <?php endforeach ?>
            </div>
        </div>

        <!-- AI予測 -->
        <?php if (!empty($aiAnalysis['predictions'])): ?>
        <div class="trending-section">
            <h2 class="trending-title">🔮 今後の予測</h2>
            <?php foreach ($aiAnalysis['predictions'] as $prediction): ?>
            <div class="prediction-card">
                <div class="prediction-content">
                    <?php echo htmlspecialchars($prediction['content']) ?>
                </div>
            </div>
            <?php endforeach ?>
        </div>
        <?php endif ?>
    </div>
</body>
</html>