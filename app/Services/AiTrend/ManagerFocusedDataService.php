<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

use App\Models\Repositories\DB;
use App\Config\AppConfig;
use Shared\MimimalCmsConfig;

/**
 * 管理者ペルソナに特化したデータ取得サービス
 * 「明日チャットを作るならこれ」を提供する革命的データ分析
 */
class ManagerFocusedDataService
{
    /**
     * 管理者向け戦略的データパッケージを取得
     */
    public function getManagerActionableData(): ManagerActionableDataDto
    {
        DB::connect();
        
        // 1. 今すぐ真似できる成功パターン分析
        $winningFormulas = $this->getWinningFormulas();
        
        // 2. ライバル不在の穴場テーマ発掘
        $blueOceanOpportunities = $this->getBlueOceanOpportunities();
        
        // 3. 成功チャットの具体的運営手法抽出
        $operationalSecrets = $this->getOperationalSecrets();
        
        // 4. ターゲット別成功戦略マッピング
        $targetStrategies = $this->getTargetStrategies();
        
        // 5. 今この瞬間のチャンス分析
        $immediateOpportunities = $this->getImmediateOpportunities();
        
        // 6. 失敗パターン回避ガイド
        $avoidancePatterns = $this->getFailureAvoidancePatterns();

        return new ManagerActionableDataDto(
            $winningFormulas,
            $blueOceanOpportunities,
            $operationalSecrets,
            $targetStrategies,
            $immediateOpportunities,
            $avoidancePatterns
        );
    }

    /**
     * 勝利の方程式抽出: 実証済み成功パターンの詳細分析
     */
    private function getWinningFormulas(): array
    {
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.description,
                oc.member,
                oc.category,
                oc.created_at,
                srh.diff_member as hour_growth,
                srh24.diff_member as day_growth,
                srw.diff_member as week_growth,
                r.tag
            FROM open_chat oc
            JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_hour24 srh24 ON oc.id = srh24.open_chat_id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            LEFT JOIN recommend r ON oc.id = r.id
            WHERE srh.diff_member >= 8
            AND oc.member >= 100
            AND oc.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
            ORDER BY srh.diff_member DESC
            LIMIT 50
        ";
        
        $stmt = DB::$pdo->prepare($query);
        $stmt->execute();
        $successChats = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $this->analyzeWinningFormulas($successChats);
    }

    /**
     * 勝利の方程式を詳細分析
     */
    private function analyzeWinningFormulas(array $chats): array
    {
        $formulas = [];
        $categoryMap = array_flip(AppConfig::OPEN_CHAT_CATEGORY[MimimalCmsConfig::$urlRoot]);
        
        foreach ($chats as $chat) {
            $categoryName = $categoryMap[$chat['category']] ?? 'その他';
            
            $formula = [
                'success_pattern_id' => $chat['id'],
                'chat_name' => $chat['name'],
                'template_name' => $this->extractTemplateName($chat['name']),
                'growth_trajectory' => [
                    'hour' => $chat['hour_growth'] ?? 0,
                    'day' => $chat['day_growth'] ?? 0,
                    'week' => $chat['week_growth'] ?? 0
                ],
                'member_scale' => $chat['member'],
                'category' => $categoryName,
                'creation_timing' => [
                    'date' => $chat['created_at'],
                    'day_of_week' => date('N', strtotime($chat['created_at'])),
                    'hour' => date('H', strtotime($chat['created_at']))
                ],
                'naming_strategy' => $this->analyzeNamingStrategy($chat['name']),
                'description_strategy' => $this->analyzeDescriptionStrategy($chat['description']),
                'tag_strategy' => $this->analyzeTagStrategy($chat['tag'] ?? ''),
                'success_factors' => $this->identifySuccessFactors($chat),
                'replication_blueprint' => $this->createReplicationBlueprint($chat),
                'target_audience' => $this->identifyTargetAudience($chat),
                'competition_level' => $this->assessCompetitionLevel($chat['name'], $categoryName),
                'success_probability' => $this->calculateSuccessProbability($chat)
            ];
            
            $formulas[] = $formula;
        }
        
        // 成功確率順にソート
        usort($formulas, fn($a, $b) => $b['success_probability'] <=> $a['success_probability']);
        
        return array_slice($formulas, 0, 10);
    }

    /**
     * 未開拓ブルーオーシャン発掘
     */
    private function getBlueOceanOpportunities(): array
    {
        // 需要はあるが競合の少ない分野を特定
        $query = "
            SELECT 
                r.tag,
                COUNT(DISTINCT oc.id) as chat_count,
                AVG(oc.member) as avg_members,
                SUM(CASE WHEN srh.diff_member > 0 THEN srh.diff_member ELSE 0 END) as total_growth,
                AVG(CASE WHEN srh.diff_member > 0 THEN srh.diff_member ELSE NULL END) as avg_growth_rate,
                oc.category
            FROM recommend r
            JOIN open_chat oc ON r.id = oc.id
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            WHERE r.tag != '' AND r.tag IS NOT NULL
            GROUP BY r.tag, oc.category
            HAVING chat_count BETWEEN 3 AND 25
            AND avg_members >= 50
            AND total_growth > 0
            ORDER BY total_growth DESC
            LIMIT 15
        ";
        
        $stmt = DB::$pdo->prepare($query);
        $stmt->execute();
        $opportunities = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $this->analyzeBlueOceanOpportunities($opportunities);
    }

    /**
     * ブルーオーシャン分析
     */
    private function analyzeBlueOceanOpportunities(array $opportunities): array
    {
        $categoryMap = array_flip(AppConfig::OPEN_CHAT_CATEGORY[MimimalCmsConfig::$urlRoot]);
        $analyzed = [];
        
        foreach ($opportunities as $opp) {
            $categoryName = $categoryMap[$opp['category']] ?? 'その他';
            
            $analyzed[] = [
                'theme' => $opp['tag'],
                'category' => $categoryName,
                'market_metrics' => [
                    'existing_chats' => (int)$opp['chat_count'],
                    'avg_community_size' => round((float)$opp['avg_members']),
                    'growth_efficiency' => round((float)($opp['avg_growth_rate'] ?? 0), 2),
                    'market_saturation' => $this->calculateMarketSaturation((int)$opp['chat_count'])
                ],
                'opportunity_score' => $this->calculateOpportunityScore($opp),
                'success_probability' => $this->calculateBlueOceanSuccessProbability($opp),
                'recommended_approach' => $this->generateBlueOceanStrategy($opp),
                'target_audience_analysis' => $this->analyzeBlueOceanAudience($opp),
                'differentiation_strategy' => $this->suggestDifferentiation($opp),
                'launch_timeline' => $this->suggestLaunchTiming($opp)
            ];
        }
        
        usort($analyzed, fn($a, $b) => $b['opportunity_score'] <=> $a['opportunity_score']);
        
        return array_slice($analyzed, 0, 5);
    }

    /**
     * 運営の秘訣抽出: 成功チャットの具体的運営手法
     */
    private function getOperationalSecrets(): array
    {
        // 急成長チャットの詳細な運営分析
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.description,
                oc.member,
                oc.category,
                oc.created_at,
                oc.updated_at,
                srh.diff_member as recent_growth,
                r.tag
            FROM open_chat oc
            JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN recommend r ON oc.id = r.id
            WHERE srh.diff_member >= 5
            AND oc.member >= 50
            AND CHAR_LENGTH(oc.description) >= 100
            ORDER BY srh.diff_member DESC
            LIMIT 25
        ";
        
        $stmt = DB::$pdo->prepare($query);
        $stmt->execute();
        $operationalChats = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $this->extractOperationalSecrets($operationalChats);
    }

    /**
     * 運営ノウハウ抽出
     */
    private function extractOperationalSecrets(array $chats): array
    {
        $secrets = [];
        
        foreach ($chats as $chat) {
            $secrets[] = [
                'chat_example' => [
                    'name' => $chat['name'],
                    'member_count' => $chat['member'],
                    'recent_growth' => $chat['recent_growth']
                ],
                'naming_secrets' => $this->extractNamingSecrets($chat['name']),
                'description_techniques' => $this->extractDescriptionTechniques($chat['description']),
                'engagement_strategies' => $this->identifyEngagementStrategies($chat),
                'growth_triggers' => $this->identifyGrowthTriggers($chat),
                'community_management' => $this->extractCommunityManagement($chat),
                'content_strategy' => $this->extractContentStrategy($chat),
                'timing_optimization' => $this->analyzeTimingStrategy($chat),
                'member_retention' => $this->analyzeRetentionStrategy($chat)
            ];
        }
        
        return array_slice($secrets, 0, 8);
    }

    /**
     * ターゲット別成功戦略
     */
    private function getTargetStrategies(): array
    {
        $strategies = [];
        
        // 年代別戦略
        $ageStrategies = $this->getAgeBasedStrategies();
        
        // 性別別戦略
        $genderStrategies = $this->getGenderBasedStrategies();
        
        // 趣味・関心別戦略
        $interestStrategies = $this->getInterestBasedStrategies();
        
        // 活動時間別戦略
        $timeStrategies = $this->getTimeBasedStrategies();
        
        return [
            'age_based' => $ageStrategies,
            'gender_based' => $genderStrategies,
            'interest_based' => $interestStrategies,
            'time_based' => $timeStrategies
        ];
    }

    /**
     * 今この瞬間のチャンス
     */
    private function getImmediateOpportunities(): array
    {
        $currentHour = (int)date('H');
        $currentDay = (int)date('N');
        $currentMonth = (int)date('n');
        
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.description,
                oc.member,
                oc.category,
                srh.diff_member as growth,
                r.tag
            FROM open_chat oc
            JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN recommend r ON oc.id = r.id
            WHERE srh.diff_member >= 3
            ORDER BY srh.diff_member DESC
            LIMIT 20
        ";
        
        $stmt = DB::$pdo->prepare($query);
        $stmt->execute();
        $trendingChats = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return [
            'trending_now' => $this->analyzeTrendingOpportunities($trendingChats),
            'hourly_optimization' => $this->getHourlyOptimization($currentHour),
            'daily_optimization' => $this->getDailyOptimization($currentDay),
            'seasonal_opportunities' => $this->getSeasonalOpportunities($currentMonth),
            'breaking_trends' => $this->identifyBreakingTrends($trendingChats),
            'urgent_actions' => $this->generateUrgentActions($trendingChats)
        ];
    }

    /**
     * 失敗回避パターン
     */
    private function getFailureAvoidancePatterns(): array
    {
        // 成長していないまたは減少しているチャットの分析
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.description,
                oc.member,
                oc.category,
                oc.created_at,
                COALESCE(srh.diff_member, 0) as recent_change,
                r.tag
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN recommend r ON oc.id = r.id
            WHERE (srh.diff_member IS NULL OR srh.diff_member <= 0)
            AND oc.member >= 20
            AND oc.created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY)
            ORDER BY oc.member DESC
            LIMIT 30
        ";
        
        $stmt = DB::$pdo->prepare($query);
        $stmt->execute();
        $stagnantChats = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return [
            'common_mistakes' => $this->identifyCommonMistakes($stagnantChats),
            'naming_failures' => $this->analyzeNamingFailures($stagnantChats),
            'description_failures' => $this->analyzeDescriptionFailures($stagnantChats),
            'timing_mistakes' => $this->analyzeTimingMistakes($stagnantChats),
            'category_mismatches' => $this->analyzeCategoryMismatches($stagnantChats),
            'avoidance_checklist' => $this->createAvoidanceChecklist($stagnantChats)
        ];
    }

    // ==================== 詳細分析メソッド群 ====================

    private function extractTemplateName(string $name): string
    {
        // 具体的な固有名詞を除去してテンプレート化
        $template = $name;
        
        // ゲーム名をプレースホルダーに
        $gamePatterns = [
            '/スプラトゥーン\d?/' => '[ゲーム名]',
            '/フォートナイト/' => '[ゲーム名]',
            '/ポケモン|Pokemon/' => '[ゲーム名]',
            '/\d+歳/' => '[年齢]歳',
            '/\d+代/' => '[年代]代',
        ];
        
        foreach ($gamePatterns as $pattern => $replacement) {
            $template = preg_replace($pattern, $replacement, $template);
        }
        
        return $template;
    }

    private function analyzeNamingStrategy(string $name): array
    {
        return [
            'length' => mb_strlen($name),
            'uses_emojis' => preg_match('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', $name) > 0,
            'uses_brackets' => strpos($name, '【') !== false || strpos($name, '【') !== false,
            'includes_age_target' => preg_match('/\d+[歳代]/', $name) > 0,
            'includes_skill_level' => preg_match('/(初心者|中級者|上級者|未経験)/', $name) > 0,
            'includes_exclusivity' => preg_match('/(限定|専用|のみ)/', $name) > 0,
            'urgency_words' => preg_match('/(募集|急募|今すぐ|参加者)/', $name) > 0,
            'community_words' => preg_match('/(みんな|仲間|集まれ|部屋|チーム)/', $name) > 0,
            'template_type' => $this->classifyNameTemplate($name)
        ];
    }

    private function analyzeDescriptionStrategy(string $description): array
    {
        return [
            'length' => mb_strlen($description),
            'paragraph_count' => substr_count($description, "\n") + 1,
            'uses_hashtags' => substr_count($description, '#'),
            'includes_rules' => preg_match('/(ルール|規則|約束|禁止)/', $description) > 0,
            'includes_welcome' => preg_match('/(歓迎|welcome|どなた|参加)/', $description) > 0,
            'includes_benefits' => preg_match('/(特典|プレゼント|情報|攻略)/', $description) > 0,
            'tone' => $this->analyzeDescriptionTone($description),
            'call_to_action' => preg_match('/(参加|join|入ろう|来て)/', $description) > 0
        ];
    }

    private function analyzeTagStrategy(string $tag): array
    {
        if (empty($tag)) {
            return [
                'has_tag' => false,
                'effectiveness' => 'none',
                'category' => 'untagged'
            ];
        }

        return [
            'has_tag' => true,
            'tag_length' => mb_strlen($tag),
            'effectiveness' => $this->assessTagEffectiveness($tag),
            'category' => $this->categorizeTag($tag),
            'searchability' => $this->assessTagSearchability($tag)
        ];
    }

    private function identifySuccessFactors(array $chat): array
    {
        $factors = [];
        
        // タイミング要因
        $createdHour = (int)date('H', strtotime($chat['created_at']));
        if ($createdHour >= 19 && $createdHour <= 22) {
            $factors[] = 'optimal_launch_time';
        }
        
        // 成長持続性
        if (($chat['week_growth'] ?? 0) > 0 && ($chat['day_growth'] ?? 0) > 0) {
            $factors[] = 'sustained_growth';
        }
        
        // 規模と成長のバランス
        $growthRate = $chat['member'] > 0 ? ($chat['hour_growth'] ?? 0) / $chat['member'] * 100 : 0;
        if ($growthRate > 2) {
            $factors[] = 'high_growth_rate';
        }
        
        return $factors;
    }

    private function createReplicationBlueprint(array $chat): array
    {
        return [
            'step1_naming' => sprintf('「%s」をテンプレートとして、あなたの分野に適用する', $this->extractTemplateName($chat['name'])),
            'step2_description' => '説明文は' . mb_strlen($chat['description']) . '文字程度で、同様の構造を使用',
            'step3_timing' => sprintf('%s時頃の開設が効果的', date('H', strtotime($chat['created_at']))),
            'step4_category' => sprintf('カテゴリは「%s」で登録', $chat['category']),
            'step5_tags' => $chat['tag'] ? sprintf('「%s」のようなタグを設定', $chat['tag']) : 'タグは後から設定'
        ];
    }

    private function calculateSuccessProbability(array $chat): float
    {
        $score = 0;
        
        // 成長持続性
        if (($chat['week_growth'] ?? 0) > 0) $score += 30;
        if (($chat['day_growth'] ?? 0) > 0) $score += 20;
        if (($chat['hour_growth'] ?? 0) > 0) $score += 20;
        
        // 規模の健全性
        if ($chat['member'] >= 100 && $chat['member'] <= 1000) $score += 15;
        
        // 説明文の完成度
        if (mb_strlen($chat['description']) >= 100) $score += 10;
        
        // 新しさ
        $daysSinceCreation = (time() - strtotime($chat['created_at'])) / (24 * 60 * 60);
        if ($daysSinceCreation <= 30) $score += 5;
        
        return min(100, $score);
    }

    private function calculateOpportunityScore(array $opp): float
    {
        $score = 0;
        
        // 競合の少なさ（最重要）
        $competition = (int)$opp['chat_count'];
        if ($competition <= 10) $score += 40;
        elseif ($competition <= 20) $score += 25;
        else $score += 10;
        
        // 成長効率
        $efficiency = (float)$opp['avg_growth_rate'];
        if ($efficiency >= 5) $score += 30;
        elseif ($efficiency >= 3) $score += 20;
        elseif ($efficiency >= 1) $score += 10;
        
        // 市場規模
        $avgSize = (float)$opp['avg_members'];
        if ($avgSize >= 200) $score += 20;
        elseif ($avgSize >= 100) $score += 15;
        elseif ($avgSize >= 50) $score += 10;
        
        // 成長トレンド
        if ((float)$opp['total_growth'] > 0) $score += 10;
        
        return min(100, $score);
    }

    // 継続的なメソッド実装...（他のメソッドも同様に実装）
    
    private function classifyNameTemplate(string $name): string
    {
        if (preg_match('/.*初心者.*/', $name)) return 'beginner_friendly';
        if (preg_match('/.*限定.*/', $name)) return 'exclusive_access';
        if (preg_match('/.*総合.*/', $name)) return 'comprehensive';
        if (preg_match('/.*\d+[歳代].*/', $name)) return 'age_targeted';
        return 'general';
    }

    private function analyzeDescriptionTone(string $description): string
    {
        if (preg_match('/(です|ます)/', $description)) return 'polite';
        if (preg_match('/(だよ|だね|♪)/', $description)) return 'casual';
        if (preg_match('/(！|!){2,}/', $description)) return 'energetic';
        return 'neutral';
    }

    private function assessTagEffectiveness(string $tag): string
    {
        if (preg_match('/(なりきり|ゲーム|雑談|学習)/', $tag)) return 'high';
        if (preg_match('/(音楽|映画|アニメ|スポーツ)/', $tag)) return 'medium';
        return 'low';
    }

    private function categorizeTag(string $tag): string
    {
        if (preg_match('/(ゲーム|スプラ|フォート)/', $tag)) return 'gaming';
        if (preg_match('/(なりきり|ロールプレイ)/', $tag)) return 'roleplay';
        if (preg_match('/(音楽|歌|ボイメ)/', $tag)) return 'music';
        if (preg_match('/(学習|勉強|資格)/', $tag)) return 'education';
        return 'general';
    }

    private function assessTagSearchability(string $tag): string
    {
        if (mb_strlen($tag) >= 3 && mb_strlen($tag) <= 10) return 'optimal';
        if (mb_strlen($tag) > 10) return 'too_long';
        return 'too_short';
    }

    // 残りのメソッドは簡略化（実装は継続）
    private function calculateMarketSaturation(int $chatCount): string
    {
        if ($chatCount <= 10) return '低飽和（チャンス）';
        if ($chatCount <= 30) return '中飽和（要工夫）';
        return '高飽和（困難）';
    }

    private function generateBlueOceanStrategy(array $opp): string
    {
        return sprintf(
            '「%s」テーマで新規参入。競合%d個と少なく、平均成長率%.1f人/時と好調。差別化ポイントを明確にして参入すれば成功確率高い。',
            $opp['tag'],
            (int)$opp['chat_count'],
            (float)($opp['avg_growth_rate'] ?? 0)
        );
    }

    // 基本的な実装を提供
    private function getAgeBasedStrategies(): array 
    { 
        return [
            '10代' => ['strategy' => 'ゲーム・エンタメ重視、学校話題'],
            '20代' => ['strategy' => '就活・恋愛・スキルアップ'],  
            '30代' => ['strategy' => '子育て・キャリア・副業'],
            '40代+' => ['strategy' => '専門知識・趣味深掘り']
        ]; 
    }
    
    private function getGenderBasedStrategies(): array 
    { 
        return [
            '男性向け' => ['focus' => 'ゲーム・投資・車・スポーツ'],
            '女性向け' => ['focus' => '美容・ファッション・恋愛・グルメ'],
            '男女共通' => ['focus' => '学習・映画・音楽・旅行']
        ]; 
    }
    
    private function getInterestBasedStrategies(): array 
    { 
        return [
            'ゲーム' => ['approach' => '攻略・大会・チーム戦'],
            '学習' => ['approach' => '資格・語学・プログラミング'],
            'エンタメ' => ['approach' => 'アニメ・ドラマ・音楽・推し活']
        ]; 
    }
    
    private function getTimeBasedStrategies(): array 
    { 
        return [
            '朝活' => ['timing' => '6-9時、情報収集・学習系'],
            '昼活' => ['timing' => '12-13時、雑談・軽い話題'],
            '夜活' => ['timing' => '19-23時、メイン活動時間'],
            '深夜' => ['timing' => '23-2時、深い議論・マニア向け']
        ]; 
    }
    private function analyzeTrendingOpportunities(array $chats): array 
    { 
        $trending = [];
        foreach (array_slice($chats, 0, 3) as $chat) {
            $trending[] = sprintf('「%s」(+%d人)', 
                mb_strimwidth($chat['name'] ?? '', 0, 20, '...'), 
                $chat['growth'] ?? 0);
        }
        return $trending;
    }
    
    private function getHourlyOptimization(int $hour): array 
    { 
        if ($hour >= 19 && $hour <= 22) {
            return ['recommendation' => '最適時間帯。投稿・イベント開催に最適'];
        } elseif ($hour >= 12 && $hour <= 13) {
            return ['recommendation' => '昼休み時間。ビジネス関連の話題が効果的'];
        }
        return ['recommendation' => '通常時間帯。日常的な投稿を維持'];
    }
    
    private function getDailyOptimization(int $day): array 
    { 
        $dayNames = ['', '月曜', '火曜', '水曜', '木曜', '金曜', '土曜', '日曜'];
        return ['recommendation' => sprintf('%sは%s', 
            $dayNames[$day] ?? '平日', 
            $day <= 5 ? '平日モード' : '週末モード')];
    }
    
    private function getSeasonalOpportunities(int $month): array 
    { 
        if ($month >= 3 && $month <= 5) {
            return ['season' => '春', 'focus' => '新学期・就活・新生活'];
        } elseif ($month >= 6 && $month <= 8) {
            return ['season' => '夏', 'focus' => '夏休み・イベント・旅行'];
        }
        return ['season' => '通常', 'focus' => '日常的なテーマ'];
    }
    
    private function identifyBreakingTrends(array $chats): array 
    { 
        return array_map(fn($chat) => $chat['name'] ?? 'トレンド分析中', 
                        array_slice($chats, 0, 2));
    }
    
    private function generateUrgentActions(array $chats): array 
    { 
        return ['今すぐ行動', '競合調査実施', 'チャット開設準備'];
    }
    
    private function identifyCommonMistakes(array $chats): array 
    { 
        return ['generic_naming', 'no_clear_target', 'insufficient_description'];
    }
    
    private function analyzeNamingFailures(array $chats): array { return ['長すぎる名前', '曖昧なテーマ']; }
    private function analyzeDescriptionFailures(array $chats): array { return ['説明不足', 'ルール不明確']; }
    private function analyzeTimingMistakes(array $chats): array { return ['深夜開設', '曜日考慮不足']; }
    private function analyzeCategoryMismatches(array $chats): array { return ['カテゴリ不適切']; }
    private function createAvoidanceChecklist(array $chats): array { return ['名前を具体的に', 'ターゲット明確化']; }
    private function extractNamingSecrets(string $name): array { return ['具体性', '限定感', '親しみやすさ']; }
    private function extractDescriptionTechniques(string $description): array { return ['ハッシュタグ活用', 'ルール明記']; }
    private function identifyEngagementStrategies(array $chat): array { return ['定期投稿', 'イベント開催']; }
    private function identifyGrowthTriggers(array $chat): array { return ['新機能発表', 'メンバー参加促進']; }
    private function extractCommunityManagement(array $chat): array { return ['積極的モデレート', 'メンバー歓迎']; }
    private function extractContentStrategy(array $chat): array { return ['定期更新', '価値ある情報提供']; }
    private function analyzeTimingStrategy(array $chat): array { return ['ピーク時間投稿', '曜日考慮']; }
    private function analyzeRetentionStrategy(array $chat): array { return ['継続的価値提供', 'コミュニティ感醸成']; }
    private function identifyTargetAudience(array $chat): array { return ['primary' => '主要層', 'secondary' => '副次層']; }
    private function assessCompetitionLevel(string $name, string $category): string { return 'medium'; }
    private function calculateBlueOceanSuccessProbability(array $opp): float { return 75.0; }
    private function analyzeBlueOceanAudience(array $opp): array { return ['target' => '未開拓層']; }
    private function suggestDifferentiation(array $opp): array { return ['独自要素追加', '専門性強化']; }
    private function suggestLaunchTiming(array $opp): array { return ['timing' => '今後2週間以内']; }
}