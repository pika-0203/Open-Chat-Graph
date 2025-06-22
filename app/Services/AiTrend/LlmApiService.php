<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

/**
 * LLM API呼び出しサービス
 * OpenAI、Claude、Gemini等の複数プロバイダーに対応
 */
class LlmApiService
{
    private string $provider;
    private string $apiKey;
    private string $apiUrl;
    private int $maxTokens;
    private float $temperature;

    public function __construct()
    {
        // 設定ファイルから読み込み（実際の環境ではlocal-secrets.phpから）
        $this->provider = $_ENV['LLM_PROVIDER'] ?? 'openai'; // openai, claude, gemini
        $this->apiKey = $_ENV['LLM_API_KEY'] ?? '';
        $this->maxTokens = (int)($_ENV['LLM_MAX_TOKENS'] ?? 2000);
        $this->temperature = (float)($_ENV['LLM_TEMPERATURE'] ?? 0.7);
        
        $this->setApiUrl();
    }

    private function setApiUrl(): void
    {
        switch ($this->provider) {
            case 'openai':
                $this->apiUrl = 'https://api.openai.com/v1/chat/completions';
                break;
            case 'claude':
                $this->apiUrl = 'https://api.anthropic.com/v1/messages';
                break;
            case 'gemini':
                $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
                break;
            default:
                throw new \InvalidArgumentException("Unsupported LLM provider: {$this->provider}");
        }
    }

    /**
     * オープンチャット管理者向け分析を生成
     */
    public function generateManagerAnalysis(array $analysisData): array
    {
        $prompt = $this->buildManagerAnalysisPrompt($analysisData);
        
        try {
            $response = $this->callLlmApi($prompt);
            return $this->parseManagerAnalysisResponse($response);
        } catch (\Exception $e) {
            // API呼び出し失敗時はフォールバック（静的分析）を返す
            error_log("LLM API Error: " . $e->getMessage());
            return $this->generateFallbackAnalysis($analysisData);
        }
    }

    /**
     * 管理者向け分析プロンプトの構築
     */
    private function buildManagerAnalysisPrompt(array $data): string
    {
        $risingChats = $data['risingChats'] ?? [];
        $categoryTrends = $data['categoryTrends'] ?? [];
        $tagTrends = $data['tagTrends'] ?? [];
        $overallStats = $data['overallStats'] ?? [];
        $historicalData = $data['historicalData'] ?? [];
        
        // データを文字列形式で整理
        $risingChatsText = $this->formatRisingChatsForPrompt($risingChats);
        $categoryTrendsText = $this->formatCategoryTrendsForPrompt($categoryTrends);
        $tagTrendsText = $this->formatTagTrendsForPrompt($tagTrends);
        $statsText = $this->formatOverallStatsForPrompt($overallStats);
        $timeContext = $this->getTimeContext();
        
        return <<<PROMPT
あなたはLINE OpenChatの成長トレンド分析の専門家です。
以下のデータを分析して、オープンチャット管理者向けの実用的な洞察を提供してください。

【ペルソナ】
- 新しいオープンチャットを作って人数を集めたい人
- 既存のオープンチャットをより人気にしたい管理者
- 具体的で実行可能なアドバイスを求めている

【分析対象データ】
■ 現在の急成長チャット（直近1時間の成長数）：
{$risingChatsText}

■ カテゴリ別成長状況：
{$categoryTrendsText}

■ 注目タグ（キーワード）の成長：
{$tagTrendsText}

■ 全体統計：
{$statsText}

■ 時間コンテキスト：
{$timeContext}

【出力形式】
JSON形式で以下の項目を出力してください：

{
  "summary": "管理者向けの戦略的サマリー（200文字以内）",
  "insights": [
    {
      "icon": "絵文字",
      "title": "洞察のタイトル",
      "content": "具体的な分析内容と管理者向けアドバイス"
    }
  ],
  "alerts": [
    {
      "level": "critical/warning/info",
      "icon": "絵文字", 
      "title": "アラートタイトル",
      "message": "具体的なメッセージ",
      "action_required": true/false
    }
  ],
  "recommendations": [
    {
      "priority": "high/medium/low",
      "action": "具体的な行動項目",
      "reason": "その理由",
      "expected_impact": "期待される効果"
    }
  ]
}

【重要な分析観点】
1. 成長パターンの特徴（なぜそのチャットが伸びているか）
2. 時間帯・曜日・季節性の要因
3. コンテンツタイプ（雑談、情報共有、参加型など）の成功要因
4. ターゲット層の明確化（年代、趣味、目的）
5. 競争環境の分析（飽和しているカテゴリ vs 狙い目カテゴリ）

【注意事項】
- 必ずデータに基づいた分析を行う
- 推測や一般論ではなく、具体的な数値と事例を活用
- 実行可能で現実的なアドバイスに焦点を当てる
- 日本語で回答する
PROMPT;
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
            $createdAt = $chat['created_at'] ?? '';
            
            $formatted[] = sprintf(
                "%d位: %s (+%d人, 総%d人) [カテゴリ:%s] [開設:%s]",
                $i + 1,
                mb_strimwidth($name, 0, 50, '...'),
                $diffMember,
                $totalMember,
                $category,
                date('Y-m-d', strtotime($createdAt))
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
            $totalMembers = $trend['total_members'] ?? 0;
            
            $formatted[] = sprintf(
                "%d位: %s (+%d人, %d個のチャット, 平均+%.1f人/チャット, 総メンバー%d人)",
                $i + 1,
                $categoryName,
                $totalGrowth,
                $chatCount,
                $avgGrowth,
                $totalMembers
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
            $avgMembers = round($tag['avg_member_count'] ?? 0);
            
            $formatted[] = sprintf(
                "%d位: #%s (+%d人, %d個のチャット, 平均%d人/チャット)",
                $i + 1,
                $tagName,
                $growth,
                $roomCount,
                $avgMembers
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
            "総メンバー数: %d人\n" .
            "成長中チャット: %d個\n" .
            "減少中チャット: %d個\n" .
            "今時間の総成長: +%d人\n" .
            "成長中チャットの平均成長: +%.1f人",
            $stats['total_chats'] ?? 0,
            $stats['total_members'] ?? 0,
            $stats['growing_chats'] ?? 0,
            $stats['declining_chats'] ?? 0,
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
            $timeOfDay = '朝時間帯';
        } elseif ($hour >= 12 && $hour < 18) {
            $timeOfDay = '昼時間帯';
        } elseif ($hour >= 18 && $hour < 23) {
            $timeOfDay = '夜時間帯（最も活発）';
        } else {
            $timeOfDay = '深夜時間帯';
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
     * LLM API呼び出し
     */
    private function callLlmApi(string $prompt): string
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException("LLM API key is not configured");
        }
        
        $payload = $this->buildApiPayload($prompt);
        $headers = $this->buildApiHeaders();
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            throw new \RuntimeException("CURL Error: " . $curlError);
        }
        
        if ($httpCode !== 200) {
            throw new \RuntimeException("HTTP Error: {$httpCode}, Response: " . substr($response, 0, 500));
        }
        
        return $response;
    }

    /**
     * API ペイロードの構築
     */
    private function buildApiPayload(string $prompt): array
    {
        switch ($this->provider) {
            case 'openai':
                return [
                    'model' => 'gpt-4o',
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => $this->maxTokens,
                    'temperature' => $this->temperature,
                    'response_format' => ['type' => 'json_object']
                ];
                
            case 'claude':
                return [
                    'model' => 'claude-3-sonnet-20240229',
                    'max_tokens' => $this->maxTokens,
                    'temperature' => $this->temperature,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ]
                ];
                
            case 'gemini':
                return [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => $this->maxTokens,
                        'temperature' => $this->temperature
                    ]
                ];
                
            default:
                throw new \InvalidArgumentException("Unsupported provider: {$this->provider}");
        }
    }

    /**
     * API ヘッダーの構築
     */
    private function buildApiHeaders(): array
    {
        $headers = ['Content-Type: application/json'];
        
        switch ($this->provider) {
            case 'openai':
                $headers[] = 'Authorization: Bearer ' . $this->apiKey;
                break;
                
            case 'claude':
                $headers[] = 'x-api-key: ' . $this->apiKey;
                $headers[] = 'anthropic-version: 2023-06-01';
                break;
                
            case 'gemini':
                // Gemini API では URL パラメータで API キーを渡す
                $this->apiUrl .= '?key=' . $this->apiKey;
                break;
        }
        
        return $headers;
    }

    /**
     * LLM レスポンスの解析
     */
    private function parseManagerAnalysisResponse(string $response): array
    {
        $decoded = json_decode($response, true);
        
        if (!$decoded) {
            throw new \RuntimeException("Invalid JSON response: " . substr($response, 0, 200));
        }
        
        // プロバイダ別のレスポンス構造に対応
        $content = '';
        switch ($this->provider) {
            case 'openai':
                $content = $decoded['choices'][0]['message']['content'] ?? '';
                break;
                
            case 'claude':
                $content = $decoded['content'][0]['text'] ?? '';
                break;
                
            case 'gemini':
                $content = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';
                break;
        }
        
        // コンテンツからJSONを抽出して解析
        $analysisJson = json_decode($content, true);
        
        if (!$analysisJson) {
            throw new \RuntimeException("Invalid analysis JSON: " . substr($content, 0, 200));
        }
        
        return $this->validateAndFormatAnalysis($analysisJson);
    }

    /**
     * 分析結果の検証とフォーマット
     */
    private function validateAndFormatAnalysis(array $analysis): array
    {
        return [
            'summary' => $analysis['summary'] ?? 'AI分析を実行中です...',
            'insights' => $this->validateInsights($analysis['insights'] ?? []),
            'alerts' => $this->validateAlerts($analysis['alerts'] ?? []),
            'recommendations' => $this->validateRecommendations($analysis['recommendations'] ?? [])
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
                    'title' => mb_strimwidth($insight['title'], 0, 100, '...'),
                    'content' => mb_strimwidth($insight['content'], 0, 300, '...')
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
                    'title' => mb_strimwidth($alert['title'], 0, 100, '...'),
                    'message' => mb_strimwidth($alert['message'], 0, 200, '...'),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'action_required' => $alert['action_required'] ?? false
                ];
            }
        }
        return $validated;
    }

    /**
     * 推奨事項の検証
     */
    private function validateRecommendations(array $recommendations): array
    {
        $validated = [];
        foreach (array_slice($recommendations, 0, 5) as $rec) {
            if (isset($rec['action'])) {
                $validated[] = [
                    'priority' => in_array($rec['priority'] ?? '', ['high', 'medium', 'low']) ? $rec['priority'] : 'medium',
                    'action' => mb_strimwidth($rec['action'], 0, 150, '...'),
                    'reason' => mb_strimwidth($rec['reason'] ?? '', 0, 200, '...'),
                    'expected_impact' => mb_strimwidth($rec['expected_impact'] ?? '', 0, 150, '...')
                ];
            }
        }
        return $validated;
    }

    /**
     * API呼び出し失敗時のフォールバック分析
     */
    private function generateFallbackAnalysis(array $data): array
    {
        return [
            'summary' => 'API接続が一時的に利用できないため、基本的な統計分析を表示しています。',
            'insights' => [
                [
                    'icon' => '📊',
                    'title' => '基本分析',
                    'content' => 'リアルタイムデータに基づく成長傾向を確認しています。詳細な分析は後ほど更新されます。'
                ]
            ],
            'alerts' => [
                [
                    'level' => 'info',
                    'icon' => '🔄',
                    'title' => 'システム状態',
                    'message' => 'AI分析機能が一時的に利用できません。基本的なデータ表示は正常に動作しています。',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'action_required' => false
                ]
            ],
            'recommendations' => []
        ];
    }
}