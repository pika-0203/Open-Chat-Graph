<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

/**
 * ClaudeCode呼び出しによるLLM分析サービス
 * ローカル開発環境でClaude分析を実行
 */
class ClaudeCodeLlmService
{
    /**
     * オープンチャット管理者向け分析を生成
     */
    public function generateManagerAnalysis(array $analysisData): array
    {
        $prompt = $this->buildManagerAnalysisPrompt($analysisData);
        
        // ローカル環境ではClaudeCodeを呼び出し
        $response = $this->callLLM($prompt);
        var_dump($prompt);
        
        return $this->parseAnalysisResponse($response);
    }

    /**
     * 管理者向け分析プロンプトの構築
     * ペルソナ：オープンチャット管理者候補・運営改善を求める管理者
     */
    private function buildManagerAnalysisPrompt(array $data): string
    {
        // 新しい管理者特化データの取得
        $winningFormulas = $data['winningFormulas'] ?? [];
        $blueOceanOpportunities = $data['blueOceanOpportunities'] ?? [];
        $operationalSecrets = $data['operationalSecrets'] ?? [];
        $targetStrategies = $data['targetStrategies'] ?? [];
        $immediateOpportunities = $data['immediateOpportunities'] ?? [];
        $avoidancePatterns = $data['avoidancePatterns'] ?? [];
        
        // 従来データ（補助として）
        $risingChats = $data['risingChats'] ?? [];
        $tagTrends = $data['tagTrends'] ?? [];
        $overallStats = $data['overallStats'] ?? [];
        $timeContext = $this->getTimeContext();
        
        // 新しいデータを文字列形式で整理
        $winningFormulasText = $this->formatWinningFormulasForPrompt($winningFormulas);
        $blueOceanText = $this->formatBlueOceanForPrompt($blueOceanOpportunities);
        $operationalSecretsText = $this->formatOperationalSecretsForPrompt($operationalSecrets);
        $immediateOpportunitiesText = $this->formatImmediateOpportunitiesForPrompt($immediateOpportunities);
        
        // 従来データ（簡略化）
        $risingChatsText = $this->formatRisingChatsForPrompt(array_slice($risingChats, 0, 5));
        $tagTrendsText = $this->formatTagTrendsForPrompt(array_slice($tagTrends, 0, 5));
        $statsText = $this->formatOverallStatsForPrompt($overallStats);
        
        return <<<PROMPT
# LINE OpenChat管理者向け戦略的分析レポート

## ペルソナ
**ミッション**: 明日新しいオープンチャットを作成して確実に人を集めたい管理者
**課題**: 「どのテーマで」「どんな名前で」「どう運営すれば」成功するかを具体的に知りたい
**期待**: データに基づいた即実行可能な戦略を求めている

## 実証済み成功パターン（勝利の方程式）
```
{$winningFormulasText}
```

## 未開拓チャンス分野（ブルーオーシャン）
```
{$blueOceanText}
```

## 成功チャットの運営秘訣
```
{$operationalSecretsText}
```

## 今この瞬間のチャンス
```
{$immediateOpportunitiesText}
```

## 補助データ（参考用）

### 最新の急成長事例
```
{$risingChatsText}
```


### 注目タグ
```
{$tagTrendsText}
```

### 時間コンテキスト
```
{$timeContext}
```

## 求める分析内容

**ターゲット**: オープンチャット管理者（新規・既存問わず）が「明日から実行できる具体的アクション」を提供

以下のJSON形式で分析結果を出力してください：

```json
{
  "summary": "今すぐ新規作成・改修すべきチャットテーマと理由（120文字以内）",
  "insights": [
    {
      "icon": "絵文字",
      "title": "成功パターンの核心（40文字以内）",
      "content": "なぜ伸びているか＋管理者が今すぐ取り入れられる運営手法（180文字以内）"
    }
  ],
  "alerts": [
    {
      "level": "critical/warning/info",
      "icon": "絵文字", 
      "title": "チャンス・リスク（40文字以内）",
      "message": "今この瞬間のトレンドと管理者がすべき具体的行動（120文字以内）",
      "action_required": true/false
    }
  ],
  "theme_recommendations": [
    {
      "theme": "新規作成・改修すべきテーマ名",
      "reason": "なぜ今このテーマで作れば人が集まるのか（データ根拠込み）",
      "target": "具体的なターゲット層",
      "strategy": "開設初日から実行すべき運営手法・コンテンツ・投稿戦略",
      "competition": "競争激しさ（高/中/低）",
      "growth_potential": "成長ポテンシャル（高/中/低）"
    }
  ]
}
```

## 重要な分析観点

**目標**: 管理者が「このテーマでチャットを作れば確実に人が集まる」と確信できる情報提供

1. **実行可能なチャット作成指南**
   - 具体的なチャット名の例
   - 開設初日に投稿すべき内容
   - 参加者を惹きつける運営ルール・仕組み
   - 成長軌道に乗せる最初の1週間の戦略

2. **データ根拠付きの勝ちパターン**
   - 「○○チャットが+△人」という具体的成功事例
   - なぜその手法が今の時期・時間帯に効くのか
   - 同じパターンで作れば再現できる理由

3. **ライバル分析と差別化戦略**
   - 既存の人気チャットに勝つための差別化ポイント
   - 競争が少ない今が狙い目の穴場テーマ
   - 「○○はもう飽和、△△に切り替えるべき」という判断材料

4. **運営者目線の成功確率評価**
   - 初心者でも成功しやすいテーマ vs 運営スキルが必要なテーマ
   - 短期間で結果が出るテーマ vs 長期育成型テーマ
   - 手間をかけずに人が集まるテーマの特徴

**必須要件**: 「明日チャットを作るならこれ」と言い切れる具体性で、管理者の迷いを完全に解消する分析を提供してください。
PROMPT;
    }

    /**
     * ClaudeCode呼び出し（ローカル環境）
     */
    private function callLLM(string $prompt): string
    {
        // ローカル環境でのClaudeCode呼び出しシミュレーション
        // 実際にはClaude APIまたはローカルLLMを呼び出し
        var_dump( $prompt );
        // 暫定的にモック分析を返す（実際の実装時にClaude APIに置き換え）
        return $this->generateMockClaudeResponse($prompt);
    }

    /**
     * Claude分析のモック応答（開発用）
     */
    private function generateMockClaudeResponse(string $prompt): string
    {
        // 実際のデータを元にした現実的な分析
        return json_encode([
            "summary" => "K-POPブーム継続中、ゲーム系が最大勢力。就活需要も拡大。なりきり・参加型コンテンツが安定成長。ニッチな専門分野は競争少なく狙い目。",
            "insights" => [
                [
                    "icon" => "🌟",
                    "title" => "K-POPシリアル交換が爆発的成長",
                    "content" => "Stray Kids関連チャットが+34人と急成長。シリアルコード交換、当選報告、最新情報共有が人気の要因。韓流ファン向けのリアルタイム情報交換チャットは確実に人が集まる分野。"
                ],
                [
                    "icon" => "🎮",
                    "title" => "ゲーム系が全体の21%を独占",
                    "content" => "スプラトゥーン、ロブロックス、フォートナイトが好調。単なる雑談ではなく「攻略情報共有」「チーム募集」「大会企画」など具体的な目的があるコミュニティが成功している。"
                ],
                [
                    "icon" => "💼",
                    "title" => "就活需要が26-29卒で拡大中",
                    "content" => "就活総合チャットが+15人で安定成長。企業研究、ES添削、面接対策など実用的な情報交換の場が求められている。学年別や業界別に細分化すると効果的。"
                ],
                [
                    "icon" => "🎭",
                    "title" => "なりきり文化が最大勢力を維持",
                    "content" => "#なりきりタグが+127人で全タグ1位。オリキャラ恋愛、家族ごっこ、アニメキャラ等が人気。現実と異なる人格での交流需要は根強く、創作・ロールプレイ要素を取り入れると集客力が高い。"
                ],
                [
                    "icon" => "📚",
                    "title" => "専門学習系は小規模だが確実",
                    "content" => "消防設備士勉強会が+12人など、ニッチな資格・学習系が堅調。競争が少なく専門性があれば確実にファンがつく。規模は小さくても高エンゲージメントが期待できる狙い目分野。"
                ]
            ],
            "alerts" => [
                [
                    "level" => "warning",
                    "icon" => "🚨",
                    "title" => "ゲームカテゴリが過熱状態",
                    "message" => "ゲーム系が全体成長の21%を占める独走状態。競争激化前に、マイナーゲームや独自企画で差別化を図るチャンス。",
                    "action_required" => true
                ],
                [
                    "level" => "info",
                    "icon" => "🌟",
                    "title" => "韓流ブーム継続中",
                    "message" => "K-POP関連チャットが複数同時成長。シリアル交換、ファンアート、情報交換などのコンテンツが今最も集客力が高い。",
                    "action_required" => false
                ],
                [
                    "level" => "info",
                    "icon" => "🎵",
                    "title" => "参加型コンテンツが注目",
                    "message" => "ボイメ歌リレー+45人など、「一緒に何かをする」体験型コミュニティが伸び率高い。単純な雑談から脱却するヒント。",
                    "action_required" => false
                ]
            ],
            "theme_recommendations" => [
                [
                    "theme" => "K-POPシリアル交換・当選報告",
                    "reason" => "Stray Kids等で実証済みの高成長パターン。リアルタイム性と情報価値が高い。",
                    "target" => "10-20代韓流ファン",
                    "strategy" => "最新シリアル情報の即時共有、当選者の祝福文化、交換ルール明確化",
                    "competition" => "中",
                    "growth_potential" => "高"
                ],
                [
                    "theme" => "マイナーゲーム攻略・チーム募集",
                    "reason" => "ゲーム系は成長率高いが大手タイトルは競争激化。マイナータイトルは狙い目。",
                    "target" => "そのゲームの熱心なプレイヤー",
                    "strategy" => "攻略情報の体系化、定期イベント開催、初心者サポート",
                    "competition" => "低",
                    "growth_potential" => "中"
                ],
                [
                    "theme" => "業界別就活情報交換",
                    "reason" => "就活需要拡大中。業界を絞ることで専門性と価値を高められる。",
                    "target" => "26-29卒の就活生",
                    "strategy" => "業界OB・OGの招待、企業別攻略法共有、面接体験談蓄積",
                    "competition" => "中",
                    "growth_potential" => "高"
                ],
                [
                    "theme" => "ニッチな資格・技能学習",
                    "reason" => "専門分野は競争少なく確実。消防設備士等で成長実績あり。",
                    "target" => "その分野の学習者・従事者",
                    "strategy" => "定期勉強会、過去問共有、合格者体験談、実務相談",
                    "competition" => "低",
                    "growth_potential" => "中"
                ],
                [
                    "theme" => "オリジナル創作なりきり",
                    "reason" => "なりきり需要は巨大だが、オリジナル世界観で差別化可能。",
                    "target" => "創作・ロールプレイ好き",
                    "strategy" => "独自世界観の設定、キャラクター作成支援、ストーリー進行管理",
                    "competition" => "中",
                    "growth_potential" => "高"
                ]
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 勝利の方程式データのフォーマット
     */
    private function formatWinningFormulasForPrompt(array $formulas): string
    {
        if (empty($formulas)) {
            return "勝利の方程式データなし";
        }
        
        $formatted = [];
        foreach (array_slice($formulas, 0, 3) as $i => $formula) {
            $template = $formula['template_name'] ?? '';
            $growth = $formula['growth_trajectory']['hour'] ?? 0;
            $memberScale = $formula['member_scale'] ?? 0;
            $category = $formula['category'] ?? '';
            $successProb = $formula['success_probability'] ?? 0;
            
            $formatted[] = sprintf(
                "成功パターン%d: テンプレート「%s」(+%d人/時, 総%d人, %s) 成功確率%d%%",
                $i + 1,
                mb_strimwidth($template, 0, 40, '...'),
                $growth,
                $memberScale,
                $category,
                $successProb
            );
            
            // 運営手法の詳細
            if (!empty($formula['replication_blueprint'])) {
                $blueprint = $formula['replication_blueprint'];
                $formatted[] = "  └ 手法: " . ($blueprint['step1_naming'] ?? 'ネーミング戦略不明');
            }
        }
        
        return implode("\n", $formatted);
    }

    /**
     * ブルーオーシャンデータのフォーマット
     */
    private function formatBlueOceanForPrompt(array $opportunities): string
    {
        if (empty($opportunities)) {
            return "ブルーオーシャンデータなし";
        }
        
        $formatted = [];
        foreach (array_slice($opportunities, 0, 3) as $i => $opp) {
            $theme = $opp['theme'] ?? '';
            $existingChats = $opp['market_metrics']['existing_chats'] ?? 0;
            $avgSize = $opp['market_metrics']['avg_community_size'] ?? 0;
            $oppScore = $opp['opportunity_score'] ?? 0;
            $successProb = $opp['success_probability'] ?? 0;
            
            $formatted[] = sprintf(
                "チャンス%d: 「%s」(競合%d個, 平均%d人, チャンススコア%.1f, 成功率%d%%)",
                $i + 1,
                $theme,
                $existingChats,
                $avgSize,
                $oppScore,
                $successProb
            );
            
            if (!empty($opp['recommended_approach'])) {
                $formatted[] = "  └ 戦略: " . mb_strimwidth($opp['recommended_approach'], 0, 80, '...');
            }
        }
        
        return implode("\n", $formatted);
    }

    /**
     * 運営秘訣データのフォーマット
     */
    private function formatOperationalSecretsForPrompt(array $secrets): string
    {
        if (empty($secrets)) {
            return "運営秘訣データなし";
        }
        
        $formatted = [];
        foreach (array_slice($secrets, 0, 3) as $i => $secret) {
            $example = $secret['chat_example'] ?? [];
            $name = $example['name'] ?? '';
            $growth = $example['recent_growth'] ?? 0;
            $memberCount = $example['member_count'] ?? 0;
            
            $formatted[] = sprintf(
                "成功事例%d: 「%s」(+%d人, 総%d人)",
                $i + 1,
                mb_strimwidth($name, 0, 30, '...'),
                $growth,
                $memberCount
            );
            
            // ネーミング秘訣
            if (!empty($secret['naming_secrets'])) {
                $namingSecrets = $secret['naming_secrets'];
                if (is_array($namingSecrets)) {
                    $formatted[] = "  └ ネーミング: " . implode(', ', array_slice($namingSecrets, 0, 2));
                }
            }
            
            // エンゲージメント戦略
            if (!empty($secret['engagement_strategies'])) {
                $engagementStrategies = $secret['engagement_strategies'];
                if (is_array($engagementStrategies)) {
                    $formatted[] = "  └ 運営手法: " . implode(', ', array_slice($engagementStrategies, 0, 2));
                }
            }
        }
        
        return implode("\n", $formatted);
    }

    /**
     * 即時チャンスデータのフォーマット
     */
    private function formatImmediateOpportunitiesForPrompt(array $opportunities): string
    {
        if (empty($opportunities)) {
            return "即時チャンスデータなし";
        }
        
        $formatted = [];
        
        // トレンド中のテーマ
        if (!empty($opportunities['trending_now'])) {
            $trending = array_slice($opportunities['trending_now'], 0, 3);
            foreach ($trending as $i => $trend) {
                $formatted[] = sprintf("急上昇%d: %s", $i + 1, $trend);
            }
        }
        
        // 時間最適化
        if (!empty($opportunities['hourly_optimization'])) {
            $hourlyOpt = $opportunities['hourly_optimization'];
            $formatted[] = "時間戦略: " . (is_string($hourlyOpt) ? $hourlyOpt : '時間最適化情報あり');
        }
        
        // 緊急アクション
        if (!empty($opportunities['urgent_actions'])) {
            $urgentActions = array_slice($opportunities['urgent_actions'], 0, 2);
            foreach ($urgentActions as $i => $action) {
                $formatted[] = sprintf("緊急行動%d: %s", $i + 1, $action);
            }
        }
        
        return empty($formatted) ? "即時チャンス分析中" : implode("\n", $formatted);
    }

    /**
     * 急成長チャットデータのフォーマット
     */
    private function formatRisingChatsForPrompt(array $chats): string
    {
        if (empty($chats)) {
            return "データなし";
        }
        
        $formatted = [];
        foreach (array_slice($chats, 0, 10) as $i => $chat) {
            $name = $chat['name'] ?? '';
            $diffMember = $chat['diff_member'] ?? 0;
            $totalMember = $chat['member'] ?? 0;
            $category = $chat['category'] ?? '';
            
            $formatted[] = sprintf(
                "%d位: %s (+%d人, 総%d人) [カテゴリ:%s]",
                $i + 1,
                mb_strimwidth($name, 0, 60, '...'),
                $diffMember,
                $totalMember,
                $category
            );
        }
        
        return implode("\n", $formatted);
    }


    /**
     * タグトレンドデータのフォーマット
     */
    private function formatTagTrendsForPrompt(array $tags): string
    {
        if (empty($tags)) {
            return "データなし";
        }
        
        $formatted = [];
        foreach (array_slice($tags, 0, 15) as $i => $tag) {
            $tagName = $tag['tag'] ?? '';
            $growth = $tag['total_1h_growth'] ?? 0;
            $roomCount = $tag['room_count'] ?? 0;
            
            $formatted[] = sprintf(
                "%d位: #%s (+%d人, %d個のチャット)",
                $i + 1,
                $tagName,
                $growth,
                $roomCount
            );
        }
        
        return implode("\n", $formatted);
    }

    /**
     * 全体統計のフォーマット
     */
    private function formatOverallStatsForPrompt(array $stats): string
    {
        if (empty($stats)) {
            return "データなし";
        }
        
        return sprintf(
            "総チャット数: %d個\n" .
            "成長中チャット: %d個\n" .
            "今時間の総成長: +%d人\n" .
            "成長中チャットの平均成長: +%.1f人",
            $stats['total_chats'] ?? 0,
            $stats['growing_chats'] ?? 0,
            $stats['total_growth'] ?? 0,
            $stats['avg_growth_positive'] ?? 0
        );
    }

    /**
     * 時間コンテキストの取得
     */
    private function getTimeContext(): string
    {
        $hour = (int)date('H');
        $dayOfWeek = date('N'); // 1=月曜, 7=日曜
        $dayNames = ['', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日', '日曜日'];
        
        $timeOfDay = '';
        if ($hour >= 6 && $hour < 12) {
            $timeOfDay = '朝時間帯（通勤・通学時間）';
        } elseif ($hour >= 12 && $hour < 18) {
            $timeOfDay = '昼時間帯（昼休み・放課後）';
        } elseif ($hour >= 18 && $hour < 23) {
            $timeOfDay = '夜時間帯（最も活発な時間）';
        } else {
            $timeOfDay = '深夜時間帯（熱心なユーザーが中心）';
        }
        
        return sprintf(
            "現在時刻: %s時（%s）\n曜日: %s\n季節性: %s",
            $hour,
            $timeOfDay,
            $dayNames[$dayOfWeek] ?? '不明',
            $this->getSeasonContext()
        );
    }

    /**
     * 季節コンテキストの取得
     */
    private function getSeasonContext(): string
    {
        $month = (int)date('n');
        
        if ($month >= 3 && $month <= 5) {
            return '春（新学期・就活シーズン）';
        } elseif ($month >= 6 && $month <= 8) {
            return '夏（夏休み・イベント活発）';
        } elseif ($month >= 9 && $month <= 11) {
            return '秋（学習・資格取得シーズン）';
        } else {
            return '冬（年末年始・受験シーズン）';
        }
    }

    /**
     * 分析レスポンスの解析
     */
    private function parseAnalysisResponse(string $response): array
    {
        $decoded = json_decode($response, true);
        
        if (!$decoded) {
            // JSON解析失敗時はフォールバック
            return $this->generateFallbackAnalysis();
        }
        
        return [
            'summary' => $decoded['summary'] ?? '',
            'insights' => $this->validateInsights($decoded['insights'] ?? []),
            'alerts' => $this->validateAlerts($decoded['alerts'] ?? []),
            'theme_recommendations' => $this->validateThemeRecommendations($decoded['theme_recommendations'] ?? [])
        ];
    }

    /**
     * インサイトの検証
     */
    private function validateInsights(array $insights): array
    {
        $validated = [];
        foreach (array_slice($insights, 0, 5) as $insight) {
            if (isset($insight['title']) && isset($insight['content'])) {
                $validated[] = [
                    'icon' => $insight['icon'] ?? '📊',
                    'title' => mb_strimwidth($insight['title'], 0, 50, '...'),
                    'content' => mb_strimwidth($insight['content'], 0, 200, '...')
                ];
            }
        }
        return $validated;
    }

    /**
     * アラートの検証
     */
    private function validateAlerts(array $alerts): array
    {
        $validated = [];
        foreach (array_slice($alerts, 0, 5) as $alert) {
            if (isset($alert['title']) && isset($alert['message'])) {
                $validated[] = [
                    'level' => in_array($alert['level'] ?? '', ['critical', 'warning', 'info']) ? $alert['level'] : 'info',
                    'icon' => $alert['icon'] ?? '⚠️',
                    'title' => mb_strimwidth($alert['title'], 0, 50, '...'),
                    'message' => mb_strimwidth($alert['message'], 0, 150, '...'),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'action_required' => $alert['action_required'] ?? false
                ];
            }
        }
        return $validated;
    }

    /**
     * テーマ推奨の検証
     */
    private function validateThemeRecommendations(array $themes): array
    {
        $validated = [];
        foreach (array_slice($themes, 0, 5) as $theme) {
            if (isset($theme['theme'])) {
                $validated[] = [
                    'theme' => mb_strimwidth($theme['theme'], 0, 50, '...'),
                    'reason' => mb_strimwidth($theme['reason'] ?? '', 0, 100, '...'),
                    'target' => mb_strimwidth($theme['target'] ?? '', 0, 50, '...'),
                    'strategy' => mb_strimwidth($theme['strategy'] ?? '', 0, 100, '...'),
                    'competition' => in_array($theme['competition'] ?? '', ['高', '中', '低']) ? $theme['competition'] : '中',
                    'growth_potential' => in_array($theme['growth_potential'] ?? '', ['高', '中', '低']) ? $theme['growth_potential'] : '中'
                ];
            }
        }
        return $validated;
    }

    /**
     * フォールバック分析
     */
    private function generateFallbackAnalysis(): array
    {
        return [
            'summary' => '現在のデータに基づく基本的な分析を表示しています。',
            'insights' => [
                [
                    'icon' => '📊',
                    'title' => 'データ分析中',
                    'content' => 'リアルタイムデータの詳細分析を準備中です。'
                ]
            ],
            'alerts' => [],
            'theme_recommendations' => []
        ];
    }
}