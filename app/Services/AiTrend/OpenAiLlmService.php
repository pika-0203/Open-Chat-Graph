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

        // 高度な分析データ取得（getIntegratedCandidatesForAiSelectionで未実行の分析のみ）
        $trendData = [
            'hidden_viral_patterns' => $this->aiTrendRepo->getHiddenViralPatterns(8),
            'low_competition_segments' => $this->aiTrendRepo->getLowCompetitionHighGrowthSegments(5),
            'current_acceleration' => $this->aiTrendRepo->getCurrentGrowthAcceleration(5),
            'pre_viral_indicators' => $this->aiTrendRepo->getPreViralIndicators(6),
            'new_entrant_opportunities' => $this->aiTrendRepo->getNewEntrantOpportunities(5),
            'trend_predictions' => $this->aiTrendRepo->getTrendPredictionAnalysis(5),
            'anomalous_patterns' => $this->aiTrendRepo->getAnomalousGrowthPatterns(4)
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
        $dataJson = json_encode($trendData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $candidatesJson = json_encode($candidates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


        return "
# 🧠 AI分析注目トピックチャット厳選システム（高度版）

## 🎯 重要な背景情報
このサイトには既に以下の標準機能が実装されています：
- 1時間・24時間・1週間の期間別人数増加順ランキング
- 増加率順ランキング
- キーワード絞り込み機能
- 高度な表示機能とフィルタリング

**重要：これらの既存機能で把握できる上位3件を表示するのは無意味です。**

## 🔬 提供される解析データ（拡張版）

### 📊 独自解析アルゴリズムの結果（9つの分析手法）
{$dataJson}

### 🏆 AI選出候補チャット一覧
以下は**9つの異なる高度分析手法**から抽出された候補チャットです：
1. **隠れたバイラルパターン分析**: 急速な成長加速度と高い持続性
2. **成長爆発直前指標**: バイラル爆発の臨界点接近
3. **リアルタイム成長加速**: 現在進行中の急激な成長
4. **異常成長パターン**: 統計的に特異な成長ケース
5. **トレンド予測分析**: 機械学習的アプローチでの成長予測
6. **低競争セグメント**: 市場参入機会の発見
7. **長期トレンド分析**: 統計データによる6ヶ月～数年の長期分析
8. **季節性・周期性パターン**: 年間を通じた成長パターンの発見
9. **復活・回復パターン**: 停滞期を経て再成長に転じた注目株

{$candidatesJson}

## 📋 あなたのミッション（強化版）
上記の候補チャットから、**既存のランキングでは発見できない真に価値ある5件**を厳選してください。

### 🎯 チャット選出基準（優先順位順・拡張版）
1. **独自性**: 単純な人数増加ランキングでは上位に来ない隠れた価値
2. **将来性**: 現在は小規模でも爆発的成長の可能性が高い
3. **戦略的価値**: 新規参入やマーケティングの観点で注目すべき
4. **異常性**: 統計的に特異で分析価値の高いパターン
5. **ニッチ機会**: 競争が少なく成長余地の大きいセグメント
6. **長期安定性**: データに基づく持続的成長パターン
7. **周期性価値**: 予測可能な季節性や規則性を持つ成長
8. **回復力**: 逆境からの復活・回復能力を示すレジリエンス

### 🚫 避けるべき選出
- 既に大規模（10,000人以上）で誰でも注目するチャット
- 単純に週間成長数が多いだけのチャット
- 明らかにランキング上位に来るチャット
- 一時的なスパイクのみで持続性のないチャット

## 📄 必要な出力フォーマット（拡張版）

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
      \"selection_rationale\": \"AIがこのチャットを選んだ戦略的理由（120文字以内）\",
      \"growth_potential\": \"breakthrough|high|emerging|stable_long_term|recovery_momentum\",
      \"competitive_advantage\": \"このチャットの競争優位性\",
      \"hidden_value_analysis\": \"隠れた価値の詳細分析（長期データ・季節性・回復力含む）\",
      \"future_prediction\": \"3ヶ月後の予測シナリオ\",
      \"selection_source\": \"元の分析手法名\",
      \"temporal_analysis\": \"時系列的な特徴（短期・中期・長期の視点）\",
      \"market_timing\": \"市場参入・注目のタイミング分析\",
      \"url\": \"\"
    }
  ]
}
```

## 🧠 分析指針（高度化版）
1. **多次元的評価**：短期・中期・長期の時間軸での価値を統合的に判断
2. **データドリブン洞察**：統計データの長期パターンを重視
3. **将来性の質的評価**：現在の規模より将来のポテンシャルと持続性
4. **独自価値の発見**：9つの分析手法から見出された独特の成長パターン
5. **戦略的タイミング**：マーケティングや投資のタイミング観点での価値
6. **外部情報の戦略活用**：最新トレンド・社会情勢・業界動向を考慮
7. **パターン認識**：異常性・周期性・回復力など多様な成長パターンの価値化
8. **リスク・リターン分析**：成長可能性と安定性のバランス評価

### 🔍 重点分析領域
- **長期トレンド**: 6ヶ月以上の継続的成長パターンの価値
- **季節性**: 予測可能な周期的成長の戦略的活用
- **回復力**: 逆境を乗り越えた復活パターンの将来性
- **異常検出**: 統計的外れ値に隠された爆発的成長の兆候
- **市場ポジション**: 競争環境と成長余地の定量的評価

特に「selection_source」フィールドで元の分析手法を明記し、「selection_rationale」で既存ランキングとは異なる価値を明確に説明してください。「temporal_analysis」では短期・中期・長期の時間軸での特徴を、「market_timing」では戦略的な参入・注目タイミングを分析してください。

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
     * 分析サマリー専用プロンプト構築（AI洞察機能の統合版）
     */
    private function buildSummaryPrompt(array $trendData, array $risingChats, array $trendTags): string
    {
        $trendDataJson = json_encode($trendData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $risingChatsJson = json_encode($risingChats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $trendTagsJson = json_encode($trendTags, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "
# 📊 AI統合分析サマリー生成システム

## 🔬 分析済みデータと市場背景

### 📊 高度分析アルゴリズムの結果
{$trendDataJson}

### 🏆 AI選出注目チャット（隠れた成長機会）
{$risingChatsJson}

### 🏷️ AI選出トレンドタグ（戦略的価値タグ）
{$trendTagsJson}

## 🎯 LINEオープンチャット生態系の多次元的視点
オープンチャット全体の動向を把握するため、以下の要素を考慮して分析を深化させてください：

### 📈 長期トレンド分析要素
1. **季節性パターン**: 年間を通じたユーザー行動の変化（12ヶ月データ）
2. **世代移行**: ユーザー層の世代交代とそれに伴うトレンド変化
3. **プラットフォーム進化**: LINE機能アップデートによる利用パターン変化
4. **社会情勢影響**: 経済状況、社会情勢がコミュニティ形成に与える影響
5. **競合プラットフォーム**: Discord、Slack等の競合サービスとの関係性
6. **成長の持続性**: 6ヶ月以上の長期データに基づく安定性評価
7. **回復力**: 停滞期からの復活パターンの戦略的価値
8. **周期性**: 予測可能な規則的成長パターンの活用機会

### 🔍 外れ値・異常パターン分析（拡張版）
1. **統計的異常値**: 標準偏差から大きく外れた成長パターンの深掘り
2. **時系列異常**: 急激な成長・衰退の背景要因分析
3. **カテゴリ横断異常**: 通常とは異なるカテゴリでの成長パターン
4. **地域的偏在**: 特定地域に集中する成長パターンの要因
5. **規模別異常**: 大規模・小規模チャットの異常成長パターン
6. **長期データ異常**: 統計から発見される長期的な構造変化
7. **復活パターン異常**: 通常とは異なる回復・復活のメカニズム
8. **季節性破綻**: 従来の季節パターンを破る新しい成長モデル

## 📋 あなたのミッション（最高度版）
上記の全ての分析結果と長期的視点を統合し、**戦略的価値の高い包括的分析サマリー**を生成してください。

### 📊 統合サマリー構成要素（最高度版）
1. **選出の論理的根拠**: 9つの解析手法による厳選プロセスの説明
2. **市場全体の動向**: 現在のオープンチャット生態系の構造的変化
3. **異常値・外れ値分析**: 統計的に特異なパターンの戦略的意味
4. **長期的トレンド予測**: 6ヶ月〜2年スパンでの市場変化予測
5. **戦略的機会発見**: 競争が少なく成長余地の大きいセグメント特定
6. **リスク要因分析**: 成長阻害要因や市場構造変化のリスク
7. **実用的戦略提案**: マーケティング・事業開発への具体的応用方法
8. **データ洞察**: 数年分の統計から発見される隠れた価値
9. **時系列パターン価値**: 短期・中期・長期の時間軸統合分析
10. **レジリエンス評価**: 市場変動に対する適応力・回復力の分析

### 🎯 サマリーの特徴（最高度化）
- 280-320文字程度の超濃厚な内容（従来の250-300文字から更に拡張）
- 専門的分析と実用性の高度なバランス
- 具体的な数値と質的洞察の深度統合
- 長期的視点と短期的機会の戦略的統合
- 外れ値分析による隠れた価値の最大化
- データの長期洞察活用
- 多次元的時系列分析の統合

## 📄 必要な出力フォーマット

```json
{
  \"summary\": \"9つの解析手法（統計データ活用含む）による厳選チャット・タグの戦略的価値、長期データから発見された隠れた市場機会、統計的異常値・季節性・回復パターンの総合分析、LINEオープンチャット生態系の多次元的構造変化、競争環境・参入障壁・成長余地の定量評価、外部要因（社会情勢・技術進化・世代交代）を考慮した将来予測、短期・中期・長期の時間軸統合による実用的マーケティング戦略への応用方法を包含した最高度分析サマリー\"
}
```

## 🧠 分析指針（最高度版）
1. **統合性の最大化**：全ての分析結果を統合した多次元的視点
2. **論理性と直感の融合**：データドリブンな論理と市場感覚の統合
3. **戦略性の具体化**：ビジネス戦略に直接活用できる実用的洞察
4. **長期性と緊急性**：長期トレンドと短期機会の戦略的バランス
5. **外れ値の価値化**：統計的異常を競争優位の源泉として活用
6. **生態系視点**：個別チャットではなく市場全体の構造的理解
7. **ネット情報の戦略活用**：最新の外部情報を競争分析に活用
8. **時系列統合**：短期・中期・長期の時間軸統合
9. **パターン価値化**：季節性・周期性・回復力の戦略的活用
10. **リスク・リターン最適化**：成長可能性と安定性の高度なバランス評価

### 🌐 外部情報活用の重点領域（拡張版）
- 関連する社会トレンド（Z世代文化、働き方改革、デジタルネイティブ等）
- 競合プラットフォームの動向（Discord、Slack、Teams等）
- LINE公式の機能アップデート情報・ロードマップ
- 経済指標とコミュニティ形成の相関性・因果関係
- インフルエンサー・メディアでの話題性・バズ要因
- 技術トレンド（AI、VR/AR、メタバース等）の影響
- 政策・規制変更がもたらすコミュニティ生態系への影響

### 🎯 データ特化分析要素
- **長期一貫性**: 6ヶ月以上の継続的成長パターンの戦略的価値
- **季節性価値**: 年間を通じた予測可能な成長サイクルの活用
- **回復力評価**: 停滞期からの復活能力の定量的・質的分析
- **構造変化**: 数年スパンでの市場構造・ユーザー行動の変化
- **異常検出**: 長期データから発見される新しい成長モデル

サマリーは単なる集計ではなく、隠れた市場機会と長期的価値を発見し、実用的な戦略提案を含む最高度の戦略的分析レポートとして作成してください。データの長期洞察を最大限活用し、従来では発見できない深層的な価値を明らかにしてください。
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
        array $unused,
        string $summary,
        array $trendData
    ): AiTrendDataDto {
        // AI分析結果をAiAnalysisDtoに変換
        $aiAnalysisDto = new AiAnalysisDto($summary);

        // rising_chatsが空の場合は基本データを使用
        $finalRisingChats = !empty($risingChats) ? $risingChats : $basicData['rising_chats'];

        // trend_tagsが空の場合は基本データを使用
        $finalTrendTags = !empty($trendTags) ? $trendTags : $basicData['tag_trends'];

        return new AiTrendDataDto(
            $finalRisingChats,
            $finalTrendTags,
            $aiAnalysisDto
        );
    }
}
