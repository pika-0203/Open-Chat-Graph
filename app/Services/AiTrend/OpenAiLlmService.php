<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

use App\Config\SecretsConfig;
use App\Services\AiTrend\Repository\AiTrendAnalysisRepository;
use OpenAI;

/**
 * 高度なAIデータドリブン分析サービス
 * 独自の解析アルゴリズムを使用した多様なデータソースからの情報収集と分析
 */
class OpenAiLlmService
{
    private OpenAI\Client $openAiClient;

    public function __construct(
        private AiTrendAnalysisRepository $aiTrendRepo
    ) {
        if (empty(SecretsConfig::$openaiApiKey)) {
            throw new \InvalidArgumentException('OpenAI API key is not configured in SecretsConfig');
        }

        $this->openAiClient = OpenAI::client(SecretsConfig::$openaiApiKey);
    }

    /**
     * AI分析を実行（高度な解析アルゴリズム使用）
     */
    public function generateManagerAnalysis(): AiTrendDataDto
    {
        \App\Models\Repositories\DB::connect();

        // 高度な分析データ取得（独自アルゴリズム使用）
        $trendData = [
            'hidden_viral_patterns' => $this->aiTrendRepo->getHiddenViralPatterns(8),
            'low_competition_segments' => $this->aiTrendRepo->getLowCompetitionHighGrowthSegments(5),
            'current_acceleration' => $this->aiTrendRepo->getCurrentGrowthAcceleration(5),
            'pre_viral_indicators' => $this->aiTrendRepo->getPreViralIndicators(6),
            'new_entrant_opportunities' => $this->aiTrendRepo->getNewEntrantOpportunities(5),
            // 追加の高度分析データ
            'trend_predictions' => $this->aiTrendRepo->getTrendPredictionAnalysis(5),
            'anomalous_patterns' => $this->aiTrendRepo->getAnomalousGrowthPatterns(4)
        ];

        // AI選出用の統合候補データ取得
        $integratedCandidates = $this->aiTrendRepo->getIntegratedCandidatesForAiSelection(15);

        $basicData = $this->getBasicRealData();

        // 高度なAI分析実行（候補からの厳選）
        $aiAnalysis = $this->performAiAnalysisWithSelection($trendData, $integratedCandidates);

        return $this->buildResult($basicData, $aiAnalysis, $trendData);
    }

    /**
     * 候補選出を含む高度なAI分析プロセス
     */
    private function performAiAnalysisWithSelection(array $trendData, array $candidates): array
    {
        $prompt = $this->buildSelectionAnalysisPrompt($trendData, $candidates);
        $response = $this->callOpenAiWithRetry($prompt);
        $analysis = $this->parseAiResponse($response);

        return $analysis;
    }

    /**
     * 厳選チャット選出用プロンプト構築
     */
    private function buildSelectionAnalysisPrompt(array $trendData, array $candidates): string
    {
        $dataJson = json_encode($trendData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $candidatesJson = json_encode($candidates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // トレンドタグ選出用の参考データを取得
        $tagTrendsData = $this->getTagTrendsFromDatabase();
        $tagTrendsJson = json_encode($tagTrendsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "
# 🧠 AI分析注目トピックチャット厳選システム

## 🎯 重要な背景情報
このサイトには既に以下の標準機能が実装されています：
- 1時間・24時間・1週間の期間別人数増加順ランキング
- 増加率順ランキング
- キーワード絞り込み機能
- 高度な表示機能とフィルタリング

**重要：これらの既存機能で把握できる上位3件を表示するのは無意味です。**

## 🔬 提供される解析データ

### 📊 独自解析アルゴリズムの結果
{$dataJson}

### 🏆 AI選出候補チャット一覧
以下は6つの異なる高度分析手法から抽出された候補チャットです：
{$candidatesJson}

### 🏷️ 現在のタグトレンドデータ（参考情報）
以下は現在のタグトレンドの統計データです：
{$tagTrendsJson}

## 📋 あなたのミッション
1. 上記の候補チャットから、**既存のランキングでは発見できない真に価値ある3件**を厳選してください。
2. タグトレンドデータを分析し、**AIが戦略的に選出した真のトレンドタグ10-15個**を厳選してください。

### 🎯 チャット選出基準（優先順位順）
1. **独自性**: 単純な人数増加ランキングでは上位に来ない隠れた価値
2. **将来性**: 現在は小規模でも爆発的成長の可能性が高い
3. **戦略的価値**: 新規参入やマーケティングの観点で注目すべき
4. **異常性**: 統計的に特異で分析価値の高いパターン
5. **ニッチ機会**: 競争が少なく成長余地の大きいセグメント

### 🏷️ トレンドタグ選出基準
1. **成長性**: 単純な統計数値だけでなく、成長の質と持続性
2. **独自性**: 誰でも思いつく一般的なタグではなく、特殊な価値を持つタグ
3. **戦略性**: マーケティングやコミュニティ運営の観点で重要なタグ
4. **将来性**: 現在は小さくても将来的に大きくなる可能性が高いタグ
5. **多様性**: 様々なカテゴリやジャンルから均等に選出

### 🚫 避けるべき選出
- 既に大規模（10,000人以上）で誰でも注目するチャット
- 単純に週間成長数が多いだけのチャット・タグ
- 明らかにランキング上位に来るチャット・タグ
- 「雑談」「友達」「恋人」などの一般的すぎるタグ

## 📄 必要な出力フォーマット

```json
{
  \"rising_chats\": [
    {
      \"id\": \"チャットID\",
      \"name\": \"チャット名\",
      \"category\": \"カテゴリ名\",
      \"member_count\": メンバー数,
      \"growth_amount\": 成長量,
      \"growth_rate\": 成長率,
      \"ai_insight_score\": 95,
      \"selection_rationale\": \"AIがこのチャットを選んだ戦略的理由（100文字以内）\",
      \"growth_potential\": \"breakthrough|high|emerging\",
      \"competitive_advantage\": \"このチャットの競争優位性\",
      \"hidden_value_analysis\": \"隠れた価値の詳細分析\",
      \"future_prediction\": \"3ヶ月後の予測シナリオ\",
      \"selection_source\": \"元の分析手法名\",
      \"url\": \"\"
    }
  ],
  \"trend_tags\": [
    {
      \"tag\": \"タグ名\",
      \"growth_rate_percentage\": 成長率（パーセント）,
      \"room_count\": 関連チャット数,
      \"ai_rationale\": \"AIがこのタグを選んだ理由（50文字以内）\",
      \"growth_potential\": \"high|medium|emerging\",
      \"strategic_value\": \"このタグの戦略的価値の説明\"
    }
  ],
  \"strategic_insights\": [
    \"選出の戦略的根拠1\",
    \"選出の戦略的根拠2\",
    \"選出の戦略的根拠3\"
  ],
  \"insights\": [\"分析による洞察の配列\"],
  \"summary\": \"厳選の論理的根拠と総合分析\"
}
```

## 🧠 分析指針
1. **数値だけでなく質を重視**：成長の持続性、メンバーの質、コミュニティの健全性
2. **将来性を重視**：現在の規模より将来のポテンシャル
3. **独自価値を重視**：他の分析では見つからない独特の成長パターン
4. **戦略的価値を重視**：マーケティングやトレンド分析の観点での価値

特に「selection_source」フィールドで元の分析手法を明記し、「selection_rationale」で既存ランキングとは異なる価値を明確に説明してください。
トレンドタグについても「ai_rationale」で単純な統計ランキングとは異なるAI独自の選出理由を説明してください。
";
    }

    /**
     * 高度なデータ取得（多様なデータソースからの情報収集）
     */
    private function getBasicRealData(): array
    {
        // より包括的なデータ取得クエリ
        $advancedRisingQuery = "
            SELECT 
                oc.id,
                oc.name,
                oc.member as member_count,
                oc.category,
                oc.description,
                oc.emblem,
                oc.join_method_type,
                COALESCE(srh.diff_member, 0) as hour_growth,
                COALESCE(srd.diff_member, 0) as day_growth,
                COALESCE(srw.diff_member, 0) as week_growth,
                COALESCE(srh.percent_increase, 0) as hour_growth_rate,
                COALESCE(srd.percent_increase, 0) as day_growth_rate,
                COALESCE(srw.percent_increase, 0) as week_growth_rate,
                -- 関連タグ情報も取得
                (SELECT GROUP_CONCAT(tag SEPARATOR ', ') 
                 FROM oc_tag ot WHERE ot.id = oc.id LIMIT 5) as tags
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE oc.member >= 3
              AND (COALESCE(srw.diff_member, 0) > 0 OR 
                   COALESCE(srd.diff_member, 0) > 0 OR 
                   COALESCE(srh.diff_member, 0) > 0 OR 
                   oc.member > 50)
            ORDER BY 
                (COALESCE(srw.diff_member, 0) * 1.0 + 
                 COALESCE(srd.diff_member, 0) * 2.0 + 
                 COALESCE(srh.diff_member, 0) * 3.0) DESC, 
                oc.member DESC
            LIMIT 100
        ";

        $stmt = \App\Models\Repositories\DB::$pdo->prepare($advancedRisingQuery);
        $stmt->execute();
        $risingData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $risingChats = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'description' => $item['description'] ?? '',
                'category' => $this->getCategoryName((int)$item['category']),
                'category_id' => (int)$item['category'],
                'member_count' => (int)$item['member_count'],
                'hour_growth_amount' => (int)$item['hour_growth'],
                'day_growth_amount' => (int)$item['day_growth'],
                'week_growth_amount' => (int)$item['week_growth'],
                'hour_growth_rate' => (float)$item['hour_growth_rate'],
                'day_growth_rate' => (float)$item['day_growth_rate'],
                'week_growth_rate' => (float)$item['week_growth_rate'],
                'growth_amount' => (int)$item['week_growth'],
                'growth_rate' => (float)$item['week_growth_rate'],
                'emblem' => (int)($item['emblem'] ?? 0),
                'join_method_type' => (int)$item['join_method_type'],
                'tags' => $item['tags'] ?? '',
                'url' => ''
            ];
        }, $risingData);

        return [
            'rising_chats' => $risingChats,
            'tag_trends' => $this->getTagTrendsFromDatabase(),
            'overall_stats' => $this->getOverallStats(),
            'category_analytics' => $this->getCategoryAnalytics()
        ];
    }

    private function getCategoryName($categoryId): string
    {
        $categories = [
            1 => '学校',
            2 => '社会人',
            3 => '大学生',
            7 => '同世代',
            8 => '地域',
            16 => 'スポーツ',
            17 => 'ゲーム',
            22 => 'アニメ・漫画',
            23 => 'ホビー',
            24 => 'ライフスタイル',
            25 => 'エンタメ',
            26 => '芸能人・有名人',
            28 => '勉強・読書',
            29 => '音楽',
            30 => 'ファン',
            34 => 'キャラクター',
            35 => 'コミュニティ',
            40 => 'アプリ・ウェブサービス',
            45 => 'その他'
        ];
        return $categories[$categoryId] ?? 'その他';
    }

    /**
     * データベースからトレンドタグを取得（参考データ用）
     */
    private function getTagTrendsFromDatabase(): array
    {
        $query = "
            SELECT 
                tag as tag, 
                COUNT(*) as room_count,
                AVG(COALESCE(srw.diff_member, 0)) as avg_growth,
                AVG(COALESCE(srd.diff_member, 0)) as avg_daily_growth,
                AVG(COALESCE(srh.diff_member, 0)) as avg_hourly_growth,
                AVG(oc.member) as avg_member_size,
                COALESCE(AVG(srw.diff_member), 0) as growth_rate_percentage
            FROM oc_tag oct
            JOIN open_chat oc ON oct.id = oc.id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            WHERE tag IS NOT NULL AND tag != '' AND LENGTH(tag) >= 2
            GROUP BY tag
            HAVING room_count >= 3 AND avg_growth >= 0
            ORDER BY growth_rate_percentage DESC, room_count DESC
            LIMIT 50
        ";

        $stmt = \App\Models\Repositories\DB::$pdo->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($item) {
            return [
                'tag' => $item['tag'],
                'room_count' => (int)$item['room_count'],
                'avg_growth' => (float)$item['avg_growth'],
                'avg_daily_growth' => (float)$item['avg_daily_growth'],
                'avg_hourly_growth' => (float)$item['avg_hourly_growth'],
                'avg_member_size' => (float)$item['avg_member_size'],
                'growth_rate_percentage' => (float)$item['growth_rate_percentage']
            ];
        }, $results);
    }

    private function getOverallStats(): array
    {
        $query = "
            SELECT 
                COUNT(*) as total_chats,
                AVG(member) as avg_members,
                SUM(COALESCE(srw.diff_member, 0)) as total_week_growth,
                SUM(COALESCE(srd.diff_member, 0)) as total_day_growth,
                SUM(COALESCE(srh.diff_member, 0)) as total_hour_growth,
                COUNT(CASE WHEN COALESCE(srw.diff_member, 0) > 0 THEN 1 END) as growing_chats_week,
                COUNT(CASE WHEN COALESCE(srd.diff_member, 0) > 0 THEN 1 END) as growing_chats_day,
                COUNT(CASE WHEN COALESCE(srh.diff_member, 0) > 0 THEN 1 END) as growing_chats_hour,
                COUNT(CASE WHEN member >= 10000 THEN 1 END) as large_chats,
                COUNT(CASE WHEN member < 1000 THEN 1 END) as small_chats
            FROM open_chat oc
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
        ";

        $stmt = \App\Models\Repositories\DB::$pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }

    private function getCategoryAnalytics(): array
    {
        $query = "
            SELECT 
                oc.category,
                COUNT(*) as total_chats,
                AVG(oc.member) as avg_member_size,
                AVG(COALESCE(srw.diff_member, 0)) as avg_weekly_growth,
                COUNT(CASE WHEN COALESCE(srw.diff_member, 0) > 0 THEN 1 END) as growing_chats,
                MAX(oc.member) as largest_chat,
                COUNT(CASE WHEN oc.member >= 10000 THEN 1 END) as dominant_players
            FROM open_chat oc
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE oc.category IS NOT NULL AND oc.category > 0
            GROUP BY oc.category
            HAVING total_chats >= 10
            ORDER BY avg_weekly_growth DESC
            LIMIT 20
        ";

        $stmt = \App\Models\Repositories\DB::$pdo->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($item) {
            return [
                'category' => $this->getCategoryName((int)$item['category']),
                'category_id' => (int)$item['category'],
                'total_chats' => (int)$item['total_chats'],
                'avg_member_size' => (float)$item['avg_member_size'],
                'avg_weekly_growth' => (float)$item['avg_weekly_growth'],
                'growing_chats' => (int)$item['growing_chats'],
                'largest_chat' => (int)$item['largest_chat'],
                'dominant_players' => (int)$item['dominant_players']
            ];
        }, $results);
    }

    /**
     * OpenAI API呼び出し（リトライ機能付き）
     */
    private function callOpenAiWithRetry(string $prompt, int $maxRetries = 3): string
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = $this->openAiClient->chat()->create([
                    'model' => 'o3-mini-2025-01-31',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => '🧠 あなたは世界最高のオープンチャット市場分析専門AIです。独自の解析アルゴリズムの結果を基に、戦略的で実用的な分析結果を提供してください。単純なランキングではなく、深い洞察と予測的分析を重視してください。'
                        ],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                ]);

                return $response->choices[0]->message->content;
            } catch (\Exception $e) {
                $lastException = $e;
                error_log("OpenAI API 呼び出し失敗 (試行 {$attempt}/{$maxRetries}): " . $e->getMessage());

                if ($attempt < $maxRetries) {
                    sleep(1);
                }
            }
        }

        throw new \Exception("OpenAI API呼び出しが{$maxRetries}回失敗しました: " . $lastException->getMessage());
    }

    /**
     * AI応答をパースしてPHP配列に変換
     */
    private function parseAiResponse(string $responseText): array
    {
        if (preg_match('/```json\s*(.*?)\s*```/s', $responseText, $matches)) {
            $jsonText = $matches[1];
        } else {
            $jsonText = $responseText;
        }

        $decoded = json_decode($jsonText, true);
        return $decoded ?? [];
    }

    /**
     * 高度な結果構築
     */
    private function buildResult(array $basicData, array $aiAnalysis, array $trendData): AiTrendDataDto
    {
        // strategic_insights を insights に変換（または両方を使用）
        $insights = $aiAnalysis['insights'] ?? [];
        if (isset($aiAnalysis['strategic_insights']) && is_array($aiAnalysis['strategic_insights'])) {
            // strategic_insights からタイトルや説明を抽出して insights 形式に変換
            foreach ($aiAnalysis['strategic_insights'] as $strategicInsight) {
                if (is_string($strategicInsight)) {
                    $insights[] = $strategicInsight;
                } elseif (isset($strategicInsight['title']) && isset($strategicInsight['description'])) {
                    $insights[] = $strategicInsight['title'] . ': ' . $strategicInsight['description'];
                }
            }
        }

        $aiAnalysisDto = new AiAnalysisDto(
            $aiAnalysis['summary'] ?? $aiAnalysis['executive_summary'] ?? '高度AI分析結果',
            $insights,
            $aiAnalysis['predictive_analytics'] ?? [],
            $aiAnalysis['recommendations'] ?? $aiAnalysis['key_recommendations'] ?? [],
            $aiAnalysis['anomaly_detection_results'] ?? [],
            [], // alerts
            [] // timeSeriesForecasts
        );

        // AI分析結果の rising_chats を使用、なければ基本データを使用
        $risingChats = $basicData['rising_chats'];

        if (isset($aiAnalysis['rising_chats']) && is_array($aiAnalysis['rising_chats'])) {
            $validRisingChats = [];
            foreach ($aiAnalysis['rising_chats'] as $chat) {
                if (isset($chat['id']) && isset($chat['name'])) {
                    $validRisingChats[] = $chat;
                }
            }
            if (!empty($validRisingChats)) {
                $risingChats = $validRisingChats;
            }
        }

        // AIが生成したトレンドタグを使用、なければデータベースから取得したタグを使用
        $tagTrends = $basicData['tag_trends'];

        if (isset($aiAnalysis['trend_tags']) && is_array($aiAnalysis['trend_tags'])) {
            $validTrendTags = [];
            foreach ($aiAnalysis['trend_tags'] as $tag) {
                if (isset($tag['tag']) && !empty($tag['tag'])) {
                    // AIが生成したタグをView用のフォーマットに変換
                    $validTrendTags[] = [
                        'tag' => $tag['tag'],
                        'growth_rate_percentage' => $tag['growth_rate_percentage'] ?? 0,
                        'room_count' => $tag['room_count'] ?? 0,
                        'ai_rationale' => $tag['ai_rationale'] ?? '',
                        'growth_potential' => $tag['growth_potential'] ?? 'medium',
                        'strategic_value' => $tag['strategic_value'] ?? ''
                    ];
                }
            }
            if (!empty($validTrendTags)) {
                $tagTrends = $validTrendTags;
            }
        }

        return new AiTrendDataDto(
            $risingChats,
            $tagTrends,
            $basicData['overall_stats'],
            $aiAnalysisDto,
            $trendData, // 解析データを履歴として保存
            $aiAnalysis['market_intelligence'] ?? [] // リアルタイム指標として市場情報を保存
        );
    }
}
