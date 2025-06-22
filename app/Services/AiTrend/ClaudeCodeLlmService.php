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
        
        return $this->parseAnalysisResponse($response);
    }

    /**
     * 管理者向け分析プロンプトの構築
     * ペルソナ：オープンチャット管理者候補・運営改善を求める管理者
     */
    private function buildManagerAnalysisPrompt(array $data): string
    {
        $risingChats = $data['risingChats'] ?? [];
        $categoryTrends = $data['categoryTrends'] ?? [];
        $tagTrends = $data['tagTrends'] ?? [];
        $overallStats = $data['overallStats'] ?? [];
        $timeContext = $this->getTimeContext();
        
        // データを文字列形式で整理
        $risingChatsText = $this->formatRisingChatsForPrompt($risingChats);
        $categoryTrendsText = $this->formatCategoryTrendsForPrompt($categoryTrends);
        $tagTrendsText = $this->formatTagTrendsForPrompt($tagTrends);
        $statsText = $this->formatOverallStatsForPrompt($overallStats);
        
        return <<<PROMPT
# LINE OpenChat管理者向けトレンド分析

## ペルソナ
- **新規管理者候補**: 新しいオープンチャットを作って人数を集めたい人
- **既存管理者**: 今運営しているオープンチャットをより人気にしたい管理者
- **共通の関心**: どのようなテーマで作れば・変更すれば人が集まるかを知りたい

## 分析対象データ

### 急成長チャット（直近1時間）
```
{$risingChatsText}
```

### カテゴリ別成長状況
```
{$categoryTrendsText}
```

### 注目タグ・キーワード
```
{$tagTrendsText}
```

### 全体統計
```
{$statsText}
```

### 時間コンテキスト
```
{$timeContext}
```

## 求める分析内容

以下のJSON形式で分析結果を出力してください：

```json
{
  "summary": "管理者向けの戦略的サマリー（150文字以内）",
  "insights": [
    {
      "icon": "絵文字",
      "title": "洞察のタイトル（50文字以内）",
      "content": "具体的な分析内容と管理者向けアドバイス（200文字以内）"
    }
  ],
  "alerts": [
    {
      "level": "critical/warning/info",
      "icon": "絵文字", 
      "title": "アラートタイトル（50文字以内）",
      "message": "具体的なメッセージとアクション案（150文字以内）",
      "action_required": true/false
    }
  ],
  "theme_recommendations": [
    {
      "theme": "おすすめテーマ名",
      "reason": "なぜこのテーマが今狙い目なのか",
      "target": "想定ターゲット層",
      "strategy": "具体的な運営戦略",
      "competition": "競争激しさ（高/中/低）",
      "growth_potential": "成長ポテンシャル（高/中/低）"
    }
  ]
}
```

## 重要な分析観点

1. **成功パターンの特徴**
   - なぜそのチャットが伸びているか（コンテンツ、運営手法、タイミング）
   - 参加者が求めているもの（情報、交流、娯楽、学習など）

2. **テーマ選択の戦略**
   - 需要があるが競争が少ない狙い目分野
   - 今まさにブームが来ている分野
   - 安定して人気のある定番分野

3. **運営改善のヒント**
   - 既存チャットで取り入れられる成功要素
   - 時間帯・曜日・季節を活かした運営

4. **具体性重視**
   - 「○○系が人気」ではなく「具体的に△△というテーマで××の手法で運営」
   - 実行可能で即座に試せるアドバイス

必ずデータに基づいた具体的で実践的な分析を行い、管理者が明日から実行できるアクションを提示してください。
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
     * カテゴリトレンドデータのフォーマット
     */
    private function formatCategoryTrendsForPrompt(array $trends): string
    {
        if (empty($trends)) {
            return "データなし";
        }
        
        $formatted = [];
        foreach ($trends as $i => $trend) {
            $categoryName = $trend['category_name'] ?? 'その他';
            $totalGrowth = $trend['total_growth'] ?? 0;
            $chatCount = $trend['chat_count'] ?? 0;
            $avgGrowth = $trend['avg_growth'] ?? 0;
            
            $formatted[] = sprintf(
                "%d位: %s (+%d人, %d個のチャット, 平均+%.1f人/チャット)",
                $i + 1,
                $categoryName,
                $totalGrowth,
                $chatCount,
                $avgGrowth
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