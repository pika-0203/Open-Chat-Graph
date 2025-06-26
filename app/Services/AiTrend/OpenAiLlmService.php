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
            'hidden_viral_patterns' => $this->aiTrendRepo->getHiddenViralPatterns(8),
            'low_competition_segments' => $this->aiTrendRepo->getLowCompetitionHighGrowthSegments(5),
            'current_acceleration' => $this->aiTrendRepo->getCurrentGrowthAcceleration(5),
            'pre_viral_indicators' => $this->aiTrendRepo->getPreViralIndicators(6),
            'new_entrant_opportunities' => $this->aiTrendRepo->getNewEntrantOpportunities(5),
            'trend_predictions' => $this->aiTrendRepo->getTrendPredictionAnalysis(5),
            'anomalous_patterns' => $this->aiTrendRepo->getAnomalousGrowthPatterns(4),
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
        $dataJson = json_encode($trendData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $candidatesJson = json_encode($candidates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


        return <<<PROMPT
# 🧠 AIトレンドアナリスト兼コミュニティ戦略家システム

## 🎯 重要な背景情報
このサイトには既に以下の標準機能が実装されています：
- 期間別人数の増加順ランキング
- 増加率順ランキング
- キーワード検索

**重要：これらの既存機能で発見できるような、単純な成長ランキング上位のチャットを選出しても意味がありません。**

## 🔬 提供される解析データ
{$dataJson}

### 🏆 AI選出候補チャット一覧
以下は**14の高度な分析手法**から抽出された、多様な可能性を秘めた候補チャットです：
{$candidatesJson}

## 📋 あなたのミッション
あなたは未来予測に長けた市場アナリストです。提供されたデータを深く分析し、既存のランキングでは決して発見できない、**多様なユーザー層に響く、真に価値ある未来のトレンドチャット5件**を厳選してください。

### 🎯 チャット選出の最重要基準（新方針）

1.  **未来のトレンド予測（最重要）**:
    - **隠れた宝石の発掘**: 現在は小規模でも、近い将来大きなトレンドになる可能性を秘めたチャット（`future_growth_potential`, `emerging_trend_topic`）を最優先で選出してください。
    - **なぜ流行るのか？**: そのチャットが次に流行る理由を、社会的な背景やユーザーインサイトに基づいて具体的に説明してください。

2.  **コミュニティの健全性と活性度**:
    - **質の高い成長**: 一時的なスパイクではなく、持続的で安定した成長を見せているチャットを評価してください。これはコミュニティが健全である証拠です。
    - **活性度の推定**: チャット名、説明文、成長パターンから、メンバーが活発に交流しているか（活性度）を推定し、選考に含めてください。

3.  **多様なユーザーへのアピール**:
    - **ペルソナの多様性**: 選出する5件が、異なるタイプのユーザー（例：初心者、専門家、学生、社会人、特定の趣味を持つ人）に響くように、多様なカテゴリやテーマを網羅してください。
    - **誰のためのチャットか？**: 「このチャットは、〇〇な人に特におすすめ」というように、ターゲットユーザーを明確にしてください。

### ⚖️ 多様性確保のルール
- **必須**: 5件のうち最低4つは異なる分析手法（`selection_source`）から選出すること。
- **必須**: 3つ以上の異なるカテゴリから選出すること。
- **避ける**: 同一カテゴリから3件以上、または同一分析手法から2件以上選出すること。

### 🚫 避けるべき選出
- 誰が見てもわかる大規模チャットや、単純な成長ランキング上位のチャット。
- 持続性のない一過性の成長を見せているチャット。
- コミュニティとしての実態や魅力が感じられないチャット。

## 📄 必要な出力フォーマット（JSON構造は変更厳禁）

```json
{
  "rising_chats": [
    {
      "id": "チャットID",
      "name": "チャット名",
      "category": "カテゴリ名",
      "member_count": メンバー数,
      "growth_amount": 成長量,
      "ai_insight_score": 95,
      "score_breakdown": {
        "growth_momentum": 25,
        "market_opportunity": 20,
        "uniqueness_factor": 25,
        "timing_value": 15,
        "sustainability": 10
      },
      "selection_rationale": "【未来予測】なぜこのチャットが次に流行るのか？ 【ターゲット】どんな人におすすめか？（120文字以内）",
      "growth_potential": "breakthrough|high|emerging|stable_long_term|recovery_momentum",
      "competitive_advantage": "このチャットが持つ、他にはないユニークな強みや魅力",
      "hidden_value_analysis": "【健全性】なぜこのコミュニティが活発だと判断したか。成長の質や持続性についての分析",
      "future_prediction": "3ヶ月後、このチャットがどのように成長しているかの具体的な予測シナリオ",
      "selection_source": "元の分析手法名",
      "temporal_analysis": "時系列的な特徴（短期・中期・長期の視点）",
      "market_timing": "市場参入・注目のタイミング分析",
      "url": ""
    }
  ]
}
```

## 🧠 分析指針
1.  **未来価値の重視**: 現在の数字よりも、3ヶ月後、半年後にどれだけ価値が高まっているかを想像して分析してください。
2.  **健全性の見極め**: 安定した成長は、良いコミュニティの証です。成長の「質」に注目してください。
3.  **多様な視点**: あなたが選んだ5つのチャットで、サイト訪問者が「こんな世界があったのか」と驚くような、多様な選択肢を提示してください。
4.  **外部情報の活用**: あなたが持つ広範な知識や、必要であればWeb検索で得られる最新の社会トレンド、話題性を考慮に入れて、選定の精度を高めてください。

**最重要指示：**
- `selection_rationale`には【未来予測】と【ターゲット】を必ず含めてください。
- `hidden_value_analysis`には【健全性】の観点からの分析を必ず含めてください。
- 全てのテキストフィールドは、必ず具体的で説得力のある日本語で記述してください。
PROMPT;
    }

    /**
     * AI選出トレンドタグ専用プロンプト構築
     */
    private function buildTrendTagsPrompt(array $tagTrendsData): string
    {
        $tagTrendsJson = json_encode($tagTrendsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
# 🏷️ AI戦略的トレンドタグ発見システム

## 🎯 重要な背景情報
このサイトには既に、タグごとのチャット数や成長順のランキング機能があります。
**重要：単純な統計ランキングで上位に来るような、ありきたりなタグを選出しても価値はありません。**

## 🔬 提供されるタグトレンドデータ
{$tagTrendsJson}

## 📋 あなたのミッション
あなたは鋭い洞察力を持つトレンドハンターです。提供されたデータを分析し、**AIが戦略的に選出した、多様な興味に応える未来のトレンドタグ10-15個**を厳選してください。

### 🏷️ トレンドタグ選出基準（新方針）

1.  **将来性と兆候の発見（最重要）**:
    - **隠れたキーワード**: 現在はまだマイナーでも、熱量が高まっている、あるいはこれから伸びる兆しがある「兆候タグ」を最優先で選んでください。
    - **なぜ今注目すべきか？**: そのタグがなぜ戦略的に重要なのか、背景にあるトレンドやユーザーのインサイトを説明してください。

2.  **独自性とニッチ性**:
    - **ありきたりを避ける**: 「雑談」「ゲーム」のような一般的なものではなく、特定の興味やコミュニティに深く刺さる、具体的でユニークなタグを評価してください。
    - **熱量の高さ**: 関連するチャット数は少なくても、成長率やメンバーあたりの成長量が大きいなど、「熱量の高さ」が感じられるタグを重視してください。

3.  **戦略的な価値**:
    - **多様なジャンル**: 選出するタグのリスト全体で、様々なカテゴリや興味関心をカバーし、多くのユーザーにとって発見があるようにしてください。
    - **新しい視点の提供**: ユーザーに「こんなタグがあったのか！」という新しい発見や視点を提供するような選出を心がけてください。

### 🚫 避けるべき選出
- 「雑談」「友達」「恋人」「暇つぶし」などの、誰でも知っている一般的すぎるタグ。
- 単純に関連チャット数が多いだけの、何の発見もないタグ。
- 成長が停滞している、あるいは既にピークを過ぎたと思われるタグ。

## 📄 必要な出力フォーマット（JSON構造は変更厳禁）

```json
{
  "trend_tags": [
    {
      "tag": "タグ名",
      "room_count": 関連チャット数,
      "ai_rationale": "AIがこのタグを選んだ理由（50文字以内）",
      "growth_potential": "high|medium|emerging",
      "strategic_value": "【戦略的価値】なぜ今このタグが重要なのか？背景にあるトレンドや、どのようなユーザーに響くのかを具体的に説明"
    }
  ]
}
```

## 🧠 分析指針
1.  **表面的な数字に騙されない**: チャット数（room_count）の多さよりも、成長の勢いや将来性を重視してください。
2.  **トレンドの「兆し」を捉える**: 世の中の動きや新しい文化の萌芽を常に意識し、それに関連するキーワードを探してください。
3.  **具体的に説明する**: なぜそのタグが価値を持つのか、誰が、どのように使うと有益なのかを、「strategic_value」で具体的に記述してください。
4.  **外部情報の活用**: あなたが持つ広範な知識や、必要であればWeb検索で得られる最新の社会トレンド、話題性を考慮に入れて、選定の精度を高めてください。

**最重要指示：**
- `strategic_value`には【戦略的価値】というキーワードを必ず含め、そのタグの重要性を具体的に説明してください。
- 全てのテキストフィールドは、必ず具体的で説得力のある日本語で記述してください。
PROMPT;
    }


    /**
     * 分析サマリー専用プロンプト構築（AI洞察機能の統合版）
     */
    private function buildSummaryPrompt(array $trendData, array $risingChats, array $trendTags): string
    {
        $trendDataJson = json_encode($trendData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $risingChatsJson = json_encode($risingChats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $trendTagsJson = json_encode($trendTags, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
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

### 📊 管理者向け実践アドバイスの構成要素
1. **🎯 今すぐ実行すべきアクション**: 選出チャットから学んだ成功パターンを活用した具体的行動
2. **📅 最適な投稿タイミング**: データから見えた活動が最も効果的な時間帯・曜日
3. **💬 効果的なコンテンツ戦略**: 成長チャットで実際に機能している投稿タイプ
4. **🏷️ トレンドタグの活用法**: 選出されたタグを使った露出増加戦略
5. **🚀 成長加速のトリガー**: 急成長チャットが実践している具体的施策
6. **💎 競合が少ない穴場**: 参入障壁が低く成長余地が大きい領域
7. **📊 成功指標の設定**: 何を目標にどう測定すべきか
8. **🔄 改善サイクル**: PDCAを回すための具体的チェックポイント
9. **⚡ 緊急対応事項**: 今すぐ対処すべき問題や機会
10. **🎨 差別化戦略**: 他のチャットと差をつける具体的方法

### 🎯 サマリー作成の重要ポイント
- **分析手法の説明は一切不要** - 管理者が知りたいのは「何をすべきか」のみ
- **具体的で実行可能なアドバイス** - 今日から実践できる内容
- **成功事例の具体的紹介** - 選出されたチャットの実際の成功要因
- **数値目標の提示** - 「週3回投稿」「夜9時がベスト」など具体的指標
- **次の一手の明確化** - 読んですぐ行動に移せる内容
- **競合優位性の構築方法** - 他と差をつける具体的戦略

## 📄 必要な出力フォーマット

```json
{
  "summary": "オープンチャット管理者向けの実践的な分析サマリーを280-320文字で生成してください。以下の要素を含めてください：(1)今選出されたチャットやタグが、なぜ他のランキングでは見つからない価値があるのか具体的に説明 (2)管理者が今すぐ実行できる具体的なアクション提案（例：どのような投稿をすべきか、どのタイミングで活動すべきか、どんなコンテンツが効果的か） (3)選出されたチャット・タグから学べる成功パターンの具体例 (4)現在のトレンドを活用する最適なタイミングと方法 (5)競合が少ない穴場領域での成長戦略。分析手法の説明は一切不要です。管理者の成功に直結する実用的なアドバイスのみを提供してください。"
}
```

## 🧠 サマリー作成の基本方針
1. **実践第一主義**：理論説明ではなく実行可能なアクションのみ
2. **具体性の追求**：抽象論を排除し、数値・時間・方法を明確化
3. **成功事例ベース**：実際に成長しているチャットの具体的施策を参考に
4. **即効性重視**：今日・今週実行できることを優先
5. **差別化の明確化**：他のチャットにない独自価値の構築方法
6. **測定可能な目標**：成果を数値で追跡できる指標の設定
7. **リスク最小化**：失敗を避けるための具体的注意点
8. **継続可能性**：無理なく続けられる現実的な運営方法

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

**最重要指示：**
- サマリーは分析手法の説明ではなく、オープンチャット管理者が今すぐ実行できる具体的なアクションプランを提供してください
- 「〜分析により」「〜手法で」等の分析プロセスの説明は一切含めないでください
- 「投稿は週3回、夜9時に」「このタグを使って」「こんな内容で差別化」など具体的で実践的な指示のみを含めてください
- 読んだ管理者が明日から実行できる内容に限定してください

**重要：サマリー内容は必ず日本語で記述してください。また、抽象的な分析結果ではなく、具体的な実行指示のみを記述してください。**
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
                            'content' => '🧠 あなたは世界最高のLINEオープンチャット市場分析専門AIです。独自の解析アルゴリズムの結果を基に、戦略的で実用的な分析結果を提供してください。単純なランキングではなく、深い洞察と予測的分析を重視してください。**必ず日本語で回答してください。** 全ての応答、分析内容、説明は日本語で記述してください。'
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
