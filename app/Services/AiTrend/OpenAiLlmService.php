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
     * AI分析を実行（分割API呼び出しによる高精度化）
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
        $integratedCandidates = $this->aiTrendRepo->getIntegratedCandidatesForAiSelection(30);
        $basicData = $this->getBasicRealData();

        // 分割API呼び出しによる高精度分析
        $risingChats = $this->analyzeRisingChats($trendData, $integratedCandidates);
        $trendTags = $this->analyzeTrendTags($basicData['tag_trends']);
        $insights = $this->generateInsights($trendData, $risingChats, $trendTags);
        $summary = $this->generateSummary($trendData, $risingChats, $trendTags, $insights);

        return $this->buildResultFromSeparateAnalysis($basicData, $risingChats, $trendTags, $insights, $summary, $trendData);
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
     * AI洞察専用分析
     */
    private function generateInsights(array $trendData, array $risingChats, array $trendTags): array
    {
        $prompt = $this->buildInsightsPrompt($trendData, $risingChats, $trendTags);
        $response = $this->callOpenAiWithRetry($prompt);
        $analysis = $this->parseAiResponse($response);
        
        return $analysis['insights'] ?? [];
    }

    /**
     * 分析サマリー専用分析
     */
    private function generateSummary(array $trendData, array $risingChats, array $trendTags, array $insights): string
    {
        $prompt = $this->buildSummaryPrompt($trendData, $risingChats, $trendTags, $insights);
        $response = $this->callOpenAiWithRetry($prompt);
        $analysis = $this->parseAiResponse($response);
        
        return $analysis['summary'] ?? '';
    }

    /**
     * AI分析注目トピックチャット専用プロンプト構築
     */
    private function buildRisingChatsPrompt(array $trendData, array $candidates): string
    {
        $dataJson = json_encode($trendData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $candidatesJson = json_encode($candidates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


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

## 📋 あなたのミッション
上記の候補チャットから、**既存のランキングでは発見できない真に価値ある3件**を厳選してください。

### 🎯 チャット選出基準（優先順位順）
1. **独自性**: 単純な人数増加ランキングでは上位に来ない隠れた価値
2. **将来性**: 現在は小規模でも爆発的成長の可能性が高い
3. **戦略的価値**: 新規参入やマーケティングの観点で注目すべき
4. **異常性**: 統計的に特異で分析価値の高いパターン
5. **ニッチ機会**: 競争が少なく成長余地の大きいセグメント


### 🚫 避けるべき選出
- 既に大規模（10,000人以上）で誰でも注目するチャット
- 単純に週間成長数が多いだけのチャット
- 明らかにランキング上位に来るチャット

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
      \"ai_insight_score\": 95,
      \"selection_rationale\": \"AIがこのチャットを選んだ戦略的理由（100文字以内）\",
      \"growth_potential\": \"breakthrough|high|emerging\",
      \"competitive_advantage\": \"このチャットの競争優位性\",
      \"hidden_value_analysis\": \"隠れた価値の詳細分析\",
      \"future_prediction\": \"3ヶ月後の予測シナリオ\",
      \"selection_source\": \"元の分析手法名\",
      \"url\": \"\"
    }
  ]
}
```

## 🧠 分析指針
1. **数値だけでなく質を重視**：成長の持続性、メンバーの質、コミュニティの健全性
2. **将来性を重視**：現在の規模より将来のポテンシャル
3. **独自価値を重視**：他の分析では見つからない独特の成長パターン
4. **戦略的価値を重視**：マーケティングやトレンド分析の観点での価値
5. **ネット情報を活用**：可能な限りインターネット上の最新情報や関連する外部要因を検索・分析して判断に活用してください

特に「selection_source」フィールドで元の分析手法を明記し、「selection_rationale」で既存ランキングとは異なる価値を明確に説明してください。
分析時は必要に応じてインターネット検索を実行し、外部の最新情報やトレンド、話題性などを考慮に入れて戦略的価値を判断してください。
";
    }

    /**
     * AI選出トレンドタグ専用プロンプト構築
     */
    private function buildTrendTagsPrompt(array $tagTrendsData): string
    {
        $tagTrendsJson = json_encode($tagTrendsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "
# 🏷️ AI選出トレンドタグ厳選システム

## 🎯 重要な背景情報
このサイトには既に以下の標準機能が実装されています：
- タグ別チャット数ランキング
- タグ別成長順ランキング
- 一般的なタグ検索機能

**重要：単純な統計ランキング上位のタグを選出するのは無意味です。**

## 🔬 提供されるタグトレンドデータ
{$tagTrendsJson}

## 📋 あなたのミッション
上記のタグトレンドデータを分析し、**AIが戦略的に選出した真のトレンドタグ10-15個**を厳選してください。

### 🏷️ トレンドタグ選出基準（優先順位順）
1. **成長性**: 単純な統計数値だけでなく、成長の質と持続性
2. **独自性**: 誰でも思いつく一般的なタグではなく、特殊な価値を持つタグ
3. **戦略性**: マーケティングやコミュニティ運営の観点で重要なタグ
4. **将来性**: 現在は小さくても将来的に大きくなる可能性が高いタグ
5. **多様性**: 様々なカテゴリやジャンルから均等に選出

### 🚫 避けるべき選出
- 「雑談」「友達」「恋人」などの一般的すぎるタグ
- 単純に関連チャット数が多いだけのタグ
- 明らかに統計ランキング上位に来るタグ

## 📄 必要な出力フォーマット

```json
{
  \"trend_tags\": [
    {
      \"tag\": \"タグ名\",
      \"room_count\": 関連チャット数,
      \"ai_rationale\": \"AIがこのタグを選んだ理由（50文字以内）\",
      \"growth_potential\": \"high|medium|emerging\",
      \"strategic_value\": \"このタグの戦略的価値の説明\"
    }
  ]
}
```

## 🧠 分析指針
1. **質を重視**：単純な数量より成長の質と持続性
2. **独自性を重視**：他では見つからない特殊な価値を持つタグ
3. **戦略的価値を重視**：マーケティングやトレンド分析の観点での価値
4. **将来性を重視**：現在の規模より将来のポテンシャル
5. **ネット情報を活用**：可能な限りインターネット上の最新情報や関連する外部要因を検索・分析して判断に活用してください

「ai_rationale」で単純な統計ランキングとは異なるAI独自の選出理由を説明してください。
分析時は必要に応じてインターネット検索を実行し、外部の最新情報やトレンド、話題性などを考慮に入れて戦略的価値を判断してください。
";
    }

    /**
     * AI洞察専用プロンプト構築
     */
    private function buildInsightsPrompt(array $trendData, array $risingChats, array $trendTags): string
    {
        $trendDataJson = json_encode($trendData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $risingChatsJson = json_encode($risingChats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $trendTagsJson = json_encode($trendTags, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "
# 💡 AI洞察生成システム

## 🔬 提供される分析データ

### 📊 高度分析アルゴリズムの結果
{$trendDataJson}

### 🏆 AI選出注目チャット
{$risingChatsJson}

### 🏷️ AI選出トレンドタグ
{$trendTagsJson}

## 📋 あなたのミッション
上記の分析データを総合的に分析し、**戦略的洞察を3-5個**生成してください。

### 💡 洞察生成基準
1. **横断的分析**: チャット、タグ、統計データを横断した複合的な洞察
2. **将来予測**: 現在のトレンドから読み取れる将来の動向
3. **戦略的価値**: マーケティングやビジネス戦略に活用できる知見
4. **隠れたパターン**: 表面的には見えない深層の成長パターン
5. **市場機会**: 新しいビジネスチャンスや参入機会の示唆

### 🎯 洞察の種類
- 成長パターンの変化に関する洞察
- 新興セグメントの発見に関する洞察
- マーケット機会に関する洞察
- ユーザー行動変化に関する洞察
- 競争環境変化に関する洞察

## 📄 必要な出力フォーマット

```json
{
  \"insights\": [
    \"洞察1: 具体的で実用的な洞察内容\",
    \"洞察2: 具体的で実用的な洞察内容\",
    \"洞察3: 具体的で実用的な洞察内容\"
  ]
}
```

## 🧠 分析指針
1. **具体性を重視**：抽象的ではなく具体的で実用的な洞察
2. **データ根拠を重視**：提供されたデータに基づいた論理的な洞察
3. **戦略性を重視**：ビジネスやマーケティングに活用できる実用的な知見
4. **独自性を重視**：一般的ではない独特の視点からの洞察
5. **ネット情報を活用**：可能な限りインターネット上の最新情報や関連する外部要因を検索・分析して判断に活用してください

各洞察は50-100文字程度で、簡潔かつ具体的に記述してください。
分析時は必要に応じてインターネット検索を実行し、外部の最新情報やトレンド、話題性などを考慮に入れて戦略的価値を判断してください。
";
    }

    /**
     * 分析サマリー専用プロンプト構築
     */
    private function buildSummaryPrompt(array $trendData, array $risingChats, array $trendTags, array $insights): string
    {
        $trendDataJson = json_encode($trendData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $risingChatsJson = json_encode($risingChats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $trendTagsJson = json_encode($trendTags, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $insightsJson = json_encode($insights, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "
# 📊 AI分析サマリー生成システム

## 🔬 分析済みデータ

### 📊 高度分析アルゴリズムの結果
{$trendDataJson}

### 🏆 AI選出注目チャット
{$risingChatsJson}

### 🏷️ AI選出トレンドタグ
{$trendTagsJson}

### 💡 生成済み洞察
{$insightsJson}

## 📋 あなたのミッション
上記の全ての分析結果を統合し、**包括的な分析サマリー**を生成してください。

### 📊 サマリー構成要素
1. **選出の論理的根拠**: なぜこれらのチャット・タグが選ばれたのか
2. **トレンドの全体像**: 現在のオープンチャット市場の動向
3. **戦略的意義**: この分析結果の戦略的価値
4. **将来展望**: 予測される市場の変化
5. **実用的示唆**: 具体的な活用方法の提案

### 🎯 サマリーの特徴
- 150-200文字程度の簡潔な内容
- 専門的でありながら分かりやすい表現
- 具体的な数値や事実に基づいた説明
- 戦略的価値を明確に示す内容

## 📄 必要な出力フォーマット

```json
{
  \"summary\": \"厳選の論理的根拠と総合分析の包括的サマリー\"
}
```

## 🧠 分析指針
1. **統合性を重視**：全ての分析結果を統合した包括的な視点
2. **論理性を重視**：選出基準と結果の論理的な整合性
3. **戦略性を重視**：ビジネス戦略に活用できる実用的な総括
4. **具体性を重視**：抽象的ではなく具体的で実用的な内容
5. **ネット情報を活用**：可能な限りインターネット上の最新情報や関連する外部要因を検索・分析して判断に活用してください

サマリーは分析の価値と意義を明確に示し、読者が一目で全体像を把握できる内容にしてください。
分析時は必要に応じてインターネット検索を実行し、外部の最新情報やトレンド、話題性などを考慮に入れて戦略的価値を判断してください。
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
                            'content' => '🧠 あなたは世界最高のLINEオープンチャット市場分析専門AIです。独自の解析アルゴリズムの結果を基に、戦略的で実用的な分析結果を提供してください。単純なランキングではなく、深い洞察と予測的分析を重視してください。'
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
        array $insights, 
        string $summary, 
        array $trendData
    ): AiTrendDataDto {
        // AI分析結果をAiAnalysisDtoに変換
        $aiAnalysisDto = new AiAnalysisDto(
            $summary,
            $insights
        );

        // rising_chatsが空の場合は基本データを使用
        $finalRisingChats = !empty($risingChats) ? $risingChats : $basicData['rising_chats'];
        
        // trend_tagsが空の場合は基本データを使用
        $finalTrendTags = !empty($trendTags) ? $trendTags : $basicData['tag_trends'];

        return new AiTrendDataDto(
            $finalRisingChats,
            $finalTrendTags,
            $basicData['overall_stats'],
            $aiAnalysisDto,
            $trendData, // 解析データを履歴として保存
            [] // リアルタイム指標として空配列
        );
    }

}
