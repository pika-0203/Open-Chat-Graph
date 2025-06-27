<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

use App\Config\AppConfig;
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
     * AI分析を実行（分割API呼び出しによる高精度化）
     */
    public function generateManagerAnalysis(): AiTrendDataDto
    {
        // PHPのタイムアウトを10分に設定
        set_time_limit(600);

        \App\Models\Repositories\DB::connect();

        // AI選出用の統合候補データ取得（全ての高度分析を内部で実行済み）
        $integratedCandidates = $this->aiTrendRepo->getIntegratedCandidatesForAiSelection(30);

        // 14つの高度な分析データ取得（getIntegratedCandidatesForAiSelectionで統合済み）
        $trendData = [
            // 既存の分析手法
            'hidden_rapid_growth_patterns' => $this->aiTrendRepo->getHiddenRapidGrowthPatterns(8),
            'low_competition_segments' => $this->aiTrendRepo->getLowCompetitionHighGrowthSegments(5),
            'current_acceleration' => $this->aiTrendRepo->getCurrentGrowthAcceleration(5),
            'growth_signal_indicators' => $this->aiTrendRepo->getGrowthSignalIndicators(6),
            'new_entrant_opportunities' => $this->aiTrendRepo->getNewEntrantOpportunities(5),
            'trend_predictions' => $this->aiTrendRepo->getTrendPredictionAnalysis(5),
            'unique_patterns' => $this->aiTrendRepo->getUniqueGrowthPatterns(4),
            // 新規追加の分析手法
            'momentum_surge_analysis' => $this->aiTrendRepo->getMomentumSurgeAnalysis(6),
            'hidden_gem_analysis' => $this->aiTrendRepo->getHiddenGemAnalysis(6),
            'breakthrough_timing_analysis' => $this->aiTrendRepo->getBreakthroughTimingAnalysis(6),
            // 将来性・先見性分析手法
            'future_growth_potential' => $this->aiTrendRepo->getFutureGrowthPotentialAnalysis(5),
            'emerging_trend_topics' => $this->aiTrendRepo->getEmergingTrendTopicsAnalysis(5)
        ];

        $basicData = $this->getBasicRealData();

        // 分割API呼び出しによる高精度分析
        $risingChats = $this->analyzeRisingChats($trendData, $integratedCandidates);
        $trendTags = $this->analyzeTrendTags($basicData['tag_trends']);
        $summary = $this->generateSummary($trendData, $risingChats, $trendTags);

        return $this->buildResultFromSeparateAnalysis($basicData, $risingChats, $trendTags, [], $summary, $trendData);
    }

    /**
     * AI分析注目トピックチャット専用分析
     */
    private function analyzeRisingChats(array $trendData, array $candidates): array
    {
        $prompt = $this->buildRisingChatsPrompt($trendData, $candidates);
        $response = $this->callOpenAiWithRetry($prompt);
        $analysis = $this->parseAiResponse($response);

        return $analysis['rising_chats'] ?? [];
    }

    /**
     * AI選出トレンドタグ専用分析
     */
    private function analyzeTrendTags(array $tagTrendsData): array
    {
        $prompt = $this->buildTrendTagsPrompt($tagTrendsData);
        $response = $this->callOpenAiWithRetry($prompt);
        $analysis = $this->parseAiResponse($response);

        return $analysis['trend_tags'] ?? [];
    }


    /**
     * 分析サマリー専用分析
     */
    private function generateSummary(array $trendData, array $risingChats, array $trendTags): string
    {
        $prompt = $this->buildSummaryPrompt($trendData, $risingChats, $trendTags);
        $response = $this->callOpenAiWithRetry($prompt);
        $analysis = $this->parseAiResponse($response);

        return $analysis['summary'] ?? '';
    }

    /**
     * AI分析注目トピックチャット専用プロンプト構築
     */
    private function buildRisingChatsPrompt(array $trendData, array $candidates): string
    {
        $candidatesJson = json_encode($candidates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
# 指示

提供されたJSONデータを分析し、以下の条件に従って、今後成長が期待されるオープンチャットを5つ選出してください。

# データ

{$candidatesJson}

# 条件

1.  提供されたデータ（`member_count`, `growth_amount`など）に基づいて選出すること。
2.  なぜそのチャットが成長すると考えたか、具体的な理由を `selection_rationale` に記述すること。
3.  多様なカテゴリから選ぶこと。

# 出力フォーマット（JSON）

```json
{
  "rising_chats": [
    {
      "id": "チャットID",
      "name": "チャット名",
      "category": "カテゴリ名",
      "member_count": メンバー数,
      "growth_amount": 成長量,
      "selection_rationale": "（AIによる選出理由）"
    }
  ]
}
```
PROMPT;
    }

    /**
     * AI選出トレンドタグ専用プロンプト構築
     */
    private function buildTrendTagsPrompt(array $tagTrendsData): string
    {
        $tagTrendsJson = json_encode($tagTrendsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
# 指示

提供されたタグのJSONデータを分析し、今後トレンドになりそうなタグを10-15個選出してください。

# データ

{$tagTrendsJson}

# 条件

1.  単純に `room_count` が多いだけでなく、`avg_weekly_growth` などの成長指標も考慮すること。
2.  なぜそのタグがトレンドになると考えたか、具体的な理由を `ai_rationale` に記述すること。
3.  一般的すぎるタグ（例：「雑談」「ゲーム」）は避けること。

# 出力フォーマット（JSON）

```json
{
  "trend_tags": [
    {
      "tag": "タグ名",
      "room_count": 関連チャット数,
      "ai_rationale": "（AIによる選出理由）"
    }
  ]
}
```
PROMPT;
    }


    /**
     * 分析サマリー専用プロンプト構築
     */
    private function buildSummaryPrompt(array $trendData, array $risingChats, array $trendTags): string
    {
        $risingChatsJson = json_encode($risingChats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $trendTagsJson = json_encode($trendTags, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
# 指示

以下の「AI選出注目チャット」と「AI選出トレンドタグ」のデータを基に、オープンチャットの管理者向けに、簡潔な分析サマリーを280-320文字で作成してください。

# AI選出注目チャット
{$risingChatsJson}

# AI選出トレンドタグ
{$trendTagsJson}

# サマリーに含める要素

1.  なぜこれらのチャットやタグが注目に値するのか。
2.  管理者がすぐに実行できるアクション提案（例：投稿のヒント、注目すべきタグなど）。
3.  データから読み取れる成功パターンの要約。

# 注意事項
- 分析手法やプロセスに関する説明は不要です。
- 管理者が実践できる、具体的で分かりやすい言葉で記述してください。

# 出力フォーマット（JSON）
```json
{
  "summary": "（ここにサマリーを記述）"
}
```
PROMPT;
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
                'growth_amount' => (int)$item['week_growth'],
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
        $categories = array_flip(AppConfig::OPEN_CHAT_CATEGORY['']);
        return $categoryId ? ($categories[$categoryId] ?? 'その他') : '未分類';
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
                COALESCE(AVG(srw.diff_member), 0) as avg_weekly_growth
            FROM oc_tag oct
            JOIN open_chat oc ON oct.id = oc.id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            WHERE tag IS NOT NULL AND tag != '' AND LENGTH(tag) >= 2
            GROUP BY tag
            HAVING room_count >= 3 AND avg_growth >= 0
            ORDER BY avg_weekly_growth DESC, room_count DESC
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
                'avg_weekly_growth' => (float)$item['avg_weekly_growth']
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
                            'content' => '🧠 あなたは世界最高のLINEオープンチャット市場分析専門AIです。独自の分析結果を基に、戦略的で実用的な分析結果を提供してください。単純なランキングではなく、深い洞察と予測的分析を重視してください。**必ず日本語で回答してください。** 全ての応答、分析内容、説明は中学生でも理解できる分かりやすい日本語で記述してください。専門用語は避けて、簡単な言葉で説明してください。'
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
     * 分割API分析結果から統合結果を構築
     */
    private function buildResultFromSeparateAnalysis(
        array $basicData,
        array $risingChats,
        array $trendTags,
        array $unused,
        string $summary,
        array $trendData
    ): AiTrendDataDto {
        // AI分析結果をAiAnalysisDtoに変換
        $aiAnalysisDto = new AiAnalysisDto($summary);

        // rising_chatsが空の場合は基本データを使用
        $finalRisingChats = !empty($risingChats) ? $risingChats : $basicData['rising_chats'];

        // AI分析スコア順でソート（降順）
        if (!empty($finalRisingChats) && is_array($finalRisingChats)) {
            usort($finalRisingChats, function ($a, $b) {
                $scoreA = isset($a['ai_insight_score']) ? (int)$a['ai_insight_score'] : 0;
                $scoreB = isset($b['ai_insight_score']) ? (int)$b['ai_insight_score'] : 0;
                return $scoreB <=> $scoreA; // 降順
            });
        }

        // trend_tagsが空の場合は基本データを使用
        $finalTrendTags = !empty($trendTags) ? $trendTags : $basicData['tag_trends'];

        return new AiTrendDataDto(
            $finalRisingChats,
            $finalTrendTags,
            $aiAnalysisDto
        );
    }
}
