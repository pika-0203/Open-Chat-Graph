<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

use App\Config\SecretsConfig;
use App\Services\AiTrend\Repository\AiTrendAnalysisRepository;
use OpenAI;

/**
 * シンプルなAI分析サービス
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

        // タイムアウト設定を追加
        $this->openAiClient = OpenAI::client(SecretsConfig::$openaiApiKey);
    }

    /**
     * AI分析を実行
     */
    public function generateManagerAnalysis(): AiTrendDataDto
    {
        \App\Models\Repositories\DB::connect();

        // 基本データ取得
        $trendData =  [
            'hidden_viral_patterns' => $this->aiTrendRepo->getHiddenViralPatterns(8),
            'low_competition_segments' => $this->aiTrendRepo->getLowCompetitionHighGrowthSegments(5),
            'current_acceleration' => $this->aiTrendRepo->getCurrentGrowthAcceleration(5),
            'pre_viral_indicators' => $this->aiTrendRepo->getPreViralIndicators(6),
            'new_entrant_opportunities' => $this->aiTrendRepo->getNewEntrantOpportunities(5)
        ];

        $basicData = $this->getBasicRealData();

        // AI分析実行
        $aiAnalysis = $this->performAiAnalysis($trendData);

        return $this->buildResult($basicData, $aiAnalysis);
    }

    /**
     * 🧠 シンプルなAI分析プロセス（エラー耐性強化版）
     */
    private function performAiAnalysis(array $trendData): array
    {
        // 📊 シンプルな分析プロンプト構築
        $prompt = $this->buildSimpleAnalysisPrompt($trendData);

        // AI分析実行
        $response = $this->callOpenAiWithRetry($prompt);
        $analysis = $this->parseAiResponse($response);

        return $analysis;
    }

    /**
     * 🚀 高性能OpenAI API呼び出し（リトライ機能付き）
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
                            'content' => '🧠 あなたは世界最高のオープンチャット分析専門AIです。簡潔で実用的な分析結果を提供してください。'
                        ],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                ]);

                return $response->choices[0]->message->content;
            } catch (\Exception $e) {
                $lastException = $e;
                error_log("OpenAI API 呼び出し失敗 (試行 {$attempt}/{$maxRetries}): " . $e->getMessage());

                if ($attempt < $maxRetries) {
                    // 次の試行前に少し待機
                    sleep(1);
                }
            }
        }

        // 全ての試行が失敗した場合
        throw new \Exception("OpenAI API呼び出しが{$maxRetries}回失敗しました: " . $lastException->getMessage());
    }

    /**
     * シンプルな分析プロンプト構築
     */
    private function buildSimpleAnalysisPrompt(array $trendData): string
    {
        $dataJson = json_encode($trendData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "
# オープンチャット成長トレンド分析

## 提供データ
{$dataJson}

## 指示
上記のデータを分析し、以下のJSONフォーマットで結果を提供してください：

```json
{
  \"rising_chats\": [
    {
      \"id\": \"ID\",
      \"name\": \"名前\",
      \"category\": \"カテゴリ\",
      \"member_count\": 数値,
      \"growth_amount\": 数値,
      \"growth_rate\": 数値,
      \"ai_insight_score\": 0-100のスコア,
      \"trend_analysis\": \"成長要因の分析\",
      \"future_prediction\": \"今後の予測\",
      \"recommendation\": \"推奨事項\",
      \"url\": \"\"
    }
  ],
  \"insights\": [\"分析による洞察\"],
  \"recommendations\": [\"推奨事項\"],
  \"summary\": \"分析結果のサマリー\"
}
```

特に成長率が高く、注目すべきオープンチャットを10件程度選んで分析してください。
";
    }

    /**
     * 高度なデータ取得（AI分析用）
     */
    private function getBasicRealData(): array
    {
        $risingQuery = "
            SELECT 
                oc.id,
                oc.name,
                oc.member as member_count,
                oc.category,
                oc.description,
                COALESCE(srw.diff_member, 0) as week_growth,
                COALESCE(srd.diff_member, 0) as day_growth
            FROM open_chat oc
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            WHERE oc.member >= 5
              AND (COALESCE(srw.diff_member, 0) > 0 OR COALESCE(srd.diff_member, 0) > 0 OR oc.member > 100)
            ORDER BY COALESCE(srw.diff_member, 0) DESC, oc.member DESC
            LIMIT 50
        ";

        $stmt = \App\Models\Repositories\DB::$pdo->prepare($risingQuery);
        $stmt->execute();
        $risingData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $risingChats = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'description' => $item['description'] ?? '',
                'category' => $this->getCategoryName($item['category']),
                'member_count' => (int)$item['member_count'],
                'week_growth_amount' => (int)$item['week_growth'],
                'day_growth_amount' => (int)$item['day_growth'],
                'growth_amount' => (int)$item['week_growth'],
                'growth_rate' => 0.0,
                'url' => ''
            ];
        }, $risingData);

        return [
            'rising_chats' => $risingChats,
            'tag_trends' => $this->getTagTrends(),
            'overall_stats' => $this->getOverallStats()
        ];
    }

    private function getCategoryName($categoryId): string
    {
        $categories = [
            17 => 'ゲーム',
            26 => '芸能人・有名人',
            16 => 'スポーツ',
            7 => '同世代',
            22 => 'アニメ・漫画'
        ];
        return $categories[$categoryId] ?? 'その他';
    }

    private function getTagTrends(): array
    {
        $query = "
            SELECT tag as tag, COUNT(*) as room_count,
                   AVG(COALESCE(srw.diff_member, 0)) as avg_growth,
                   COALESCE(AVG(srw.diff_member), 0) as growth_rate_percentage
            FROM oc_tag oct
            JOIN open_chat oc ON oct.id = oc.id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE tag IS NOT NULL AND tag != ''
            GROUP BY tag
            HAVING room_count >= 5 AND avg_growth > 0
            ORDER BY growth_rate_percentage DESC
            LIMIT 20
        ";

        $stmt = \App\Models\Repositories\DB::$pdo->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($item) {
            return [
                'tag' => $item['tag'],
                'room_count' => (int)$item['room_count'],
                'avg_growth' => (float)$item['avg_growth'],
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
                COUNT(CASE WHEN COALESCE(srw.diff_member, 0) > 0 THEN 1 END) as growing_chats
            FROM open_chat oc
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
        ";

        $stmt = \App\Models\Repositories\DB::$pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * AI応答をパースしてPHP配列に変換
     */
    private function parseAiResponse(string $responseText): array
    {
        // JSONブロックを抽出
        if (preg_match('/```json\s*(.*?)\s*```/s', $responseText, $matches)) {
            $jsonText = $matches[1];
        } else {
            $jsonText = $responseText;
        }

        $decoded = json_decode($jsonText, true);
        return $decoded;
    }

    /**
     * 結果構築
     */
    private function buildResult(array $basicData, array $aiAnalysis): AiTrendDataDto
    {
        $aiAnalysisDto = new AiAnalysisDto(
            $aiAnalysis['summary'] ?? 'AI分析結果',
            $aiAnalysis['insights'] ?? [],
            [],
            $aiAnalysis['recommendations'] ?? [],
            [],
            $aiAnalysis['alerts'] ?? [],
            []
        );

        // AI分析結果のrising_chatsがある場合はそれを使用、なければ基本データを使用
        $risingChats = $basicData['rising_chats'];

        // AI分析結果のrising_chatsがある場合、検証してから使用
        if (isset($aiAnalysis['rising_chats']) && is_array($aiAnalysis['rising_chats'])) {
            $validRisingChats = [];
            foreach ($aiAnalysis['rising_chats'] as $chat) {
                // 必須フィールドが存在するかチェック
                if (isset($chat['id']) && isset($chat['name'])) {
                    $validRisingChats[] = $chat;
                }
            }
            // 有効なチャットが存在する場合のみ使用
            if (!empty($validRisingChats)) {
                $risingChats = $validRisingChats;
            }
        }

        return new AiTrendDataDto(
            $risingChats,
            $basicData['tag_trends'],
            $basicData['overall_stats'],
            $aiAnalysisDto,
            [],
            []
        );
    }
}
