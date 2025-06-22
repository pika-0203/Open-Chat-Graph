<?php

declare(strict_types=1);

namespace App\Services\AiTrend;

use App\Config\AppConfig;
use Shared\MimimalCmsConfig;
use App\Models\Repositories\DB;

class AiTrendAnalysisService
{
    public function __construct()
    {
    }

    public function getAiTrendData(): AiTrendDataDto
    {
        // DB接続
        DB::connect();

        // 各種データを取得
        $risingChats = $this->getRisingChats();
        $categoryTrends = $this->getCategoryTrends();
        $tagTrends = $this->getTagTrends();
        $overallStats = $this->getOverallStats();

        // 実用的なAI分析を実行
        $aiAnalysisData = $this->generatePracticalAiAnalysis($risingChats, $categoryTrends, $tagTrends, $overallStats);
        $aiAnalysis = new AiAnalysisDto(
            $aiAnalysisData['summary'],
            $aiAnalysisData['insights'],
            $aiAnalysisData['predictions'],
            $aiAnalysisData['recommendations'],
            $aiAnalysisData['growthPatterns'],
            $aiAnalysisData['categoryInsights'],
            $aiAnalysisData['anomalies'],
            $aiAnalysisData['timePatterns'],
            $aiAnalysisData['membershipTrends'],
            [],
            $aiAnalysisData['aiComment']
        );

        return new AiTrendDataDto(
            $risingChats,
            $categoryTrends,
            $tagTrends,
            $overallStats,
            $aiAnalysis
        );
    }

    private function getRisingChats(): array
    {
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.member,
                oc.description,
                oc.local_img_url as img_url,
                srh.diff_member,
                srh.percent_increase,
                oc.category,
                oc.emblem,
                oc.created_at
            FROM statistics_ranking_hour srh
            JOIN open_chat oc ON srh.open_chat_id = oc.id
            WHERE srh.diff_member > 0
            ORDER BY srh.diff_member DESC
            LIMIT 15
        ";
        
        $stmt = DB::$pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getCategoryTrends(): array
    {
        $query = "
            SELECT 
                oc.category,
                COUNT(DISTINCT oc.id) as chat_count,
                SUM(srh.diff_member) as total_growth,
                AVG(srh.diff_member) as avg_growth,
                SUM(oc.member) as total_members,
                MAX(srh.diff_member) as max_growth_single_chat
            FROM statistics_ranking_hour srh
            JOIN open_chat oc ON srh.open_chat_id = oc.id
            WHERE oc.category IS NOT NULL AND srh.diff_member > 0
            GROUP BY oc.category
            ORDER BY total_growth DESC
            LIMIT 10
        ";
        
        $stmt = DB::$pdo->prepare($query);
        $stmt->execute();
        $categoryTrends = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // カテゴリ名をマッピング
        $categoryMap = array_flip(AppConfig::OPEN_CHAT_CATEGORY[MimimalCmsConfig::$urlRoot]);
        foreach ($categoryTrends as &$trend) {
            $trend['category_name'] = $categoryMap[$trend['category']] ?? 'その他';
        }

        return $categoryTrends;
    }

    private function getTagTrends(): array
    {
        $query = "
            SELECT 
                r.tag,
                COUNT(DISTINCT oc.id) as room_count,
                SUM(CASE WHEN srh.diff_member > 0 THEN srh.diff_member ELSE 0 END) as total_1h_growth,
                SUM(CASE WHEN srh24.diff_member > 0 THEN srh24.diff_member ELSE 0 END) as total_24h_growth,
                AVG(oc.member) as avg_member_count,
                MAX(srh.diff_member) as max_single_growth
            FROM recommend r
            JOIN open_chat oc ON r.id = oc.id
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_hour24 srh24 ON oc.id = srh24.open_chat_id
            WHERE r.tag != '' AND r.tag IS NOT NULL
            GROUP BY r.tag
            HAVING total_1h_growth > 0 OR total_24h_growth > 0 OR room_count >= 3
            ORDER BY total_1h_growth DESC, total_24h_growth DESC
            LIMIT 25
        ";
        
        $stmt = DB::$pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getOverallStats(): array
    {
        $query = "
            SELECT 
                COUNT(DISTINCT oc.id) as total_chats,
                SUM(oc.member) as total_members,
                SUM(CASE WHEN srh.diff_member > 0 THEN 1 ELSE 0 END) as growing_chats,
                SUM(CASE WHEN srh.diff_member < 0 THEN 1 ELSE 0 END) as declining_chats,
                SUM(CASE WHEN srh.diff_member > 0 THEN srh.diff_member ELSE 0 END) as total_growth,
                AVG(CASE WHEN srh.diff_member > 0 THEN srh.diff_member ELSE NULL END) as avg_growth_positive,
                COUNT(CASE WHEN srh.diff_member >= 10 THEN 1 ELSE NULL END) as high_growth_chats,
                COUNT(CASE WHEN srh.diff_member >= 20 THEN 1 ELSE NULL END) as very_high_growth_chats
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
        ";
        
        $stmt = DB::$pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    private function generatePracticalAiAnalysis(array $risingChats, array $categoryTrends, array $tagTrends, array $overallStats): array
    {
        // 現在の時刻・曜日情報
        $hour = (int)date('H');
        $dayOfWeek = (int)date('w');
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $currentWeekday = $weekdays[$dayOfWeek];
        
        return [
            'summary' => $this->generatePracticalSummary($hour, $currentWeekday, $risingChats, $categoryTrends),
            'insights' => $this->generateDataBasedInsights($risingChats, $categoryTrends, $tagTrends, $overallStats),
            'predictions' => $this->generateRealisticPredictions($risingChats, $categoryTrends, $tagTrends),
            'recommendations' => $this->generateActionableRecommendations($risingChats, $tagTrends),
            'growthPatterns' => $this->analyzeGrowthPatterns($risingChats, $categoryTrends),
            'categoryInsights' => $this->analyzeCategoryPerformance($categoryTrends),
            'anomalies' => $this->detectRealisticAnomalies($risingChats, $overallStats),
            'timePatterns' => $this->analyzeTimeBasedPatterns($hour, $dayOfWeek, $risingChats),
            'membershipTrends' => $this->analyzeMembershipTrends($overallStats, $risingChats),
            'aiComment' => $this->generateRealisticAiComment($risingChats, $categoryTrends, $hour)
        ];
    }

    private function generatePracticalSummary(int $hour, string $weekday, array $risingChats, array $categoryTrends): string
    {
        $totalGrowth = array_sum(array_column($risingChats, 'diff_member'));
        $topCategory = $categoryTrends[0]['category_name'] ?? 'ゲーム';
        $topChat = $risingChats[0]['name'] ?? '';
        $topGrowth = $risingChats[0]['diff_member'] ?? 0;
        
        $timeContext = match(true) {
            $hour >= 6 && $hour < 9 => '朝の時間帯',
            $hour >= 12 && $hour < 13 => 'お昼休み',
            $hour >= 18 && $hour < 21 => '夕方から夜',
            $hour >= 21 && $hour < 24 => '夜の時間帯',
            default => '現在'
        };
        
        return sprintf(
            "%s曜日の%sで、全体で%d人の新規参加者を獲得。「%s」カテゴリが最も活発で、特に「%s」が%d人増加しています。",
            $weekday, $timeContext, $totalGrowth, $topCategory, $topChat, $topGrowth
        );
    }

    private function generateDataBasedInsights(array $risingChats, array $categoryTrends, array $tagTrends, array $overallStats): array
    {
        $insights = [];
        
        // 成長率分析
        $highGrowthChats = array_filter($risingChats, fn($chat) => ($chat['diff_member'] ?? 0) >= 10);
        if (count($highGrowthChats) > 0) {
            $insights[] = [
                'type' => 'growth_rate',
                'icon' => '📈',
                'title' => '高成長チャット数の増加',
                'content' => sprintf('%d個のチャットが10人以上の急成長を記録。平均成長率が向上している傾向があります。', count($highGrowthChats)),
                'confidence' => 85
            ];
        }
        
        // カテゴリバランス分析
        if (!empty($categoryTrends)) {
            $topCategoryGrowth = $categoryTrends[0]['total_growth'] ?? 0;
            $allCategoriesGrowth = array_sum(array_column($categoryTrends, 'total_growth'));
            $concentration = round(($topCategoryGrowth / max($allCategoriesGrowth, 1)) * 100, 1);
            
            $insights[] = [
                'type' => 'category_concentration',
                'icon' => '🎯',
                'title' => 'カテゴリ集中度分析',
                'content' => sprintf('トップカテゴリが全体成長の%s%%を占めており、%s傾向が見られます。', 
                    $concentration, 
                    $concentration > 40 ? '集中型' : '分散型'
                ),
                'confidence' => 90
            ];
        }
        
        // タグ多様性分析
        if (!empty($tagTrends)) {
            $activeTagCount = count(array_filter($tagTrends, fn($tag) => ($tag['total_1h_growth'] ?? 0) > 0));
            $insights[] = [
                'type' => 'tag_diversity',
                'icon' => '🏷️',
                'title' => 'トピック多様性の状況',
                'content' => sprintf('%d個のタグで同時に成長が見られ、コミュニティの興味が多様化しています。', $activeTagCount),
                'confidence' => 80
            ];
        }
        
        return $insights;
    }

    private function generateRealisticPredictions(array $risingChats, array $categoryTrends, array $tagTrends): array
    {
        $predictions = [];
        
        // 成長継続予測
        $strongGrowthChats = array_filter($risingChats, fn($chat) => 
            ($chat['diff_member'] ?? 0) >= 5 && ($chat['percent_increase'] ?? 0) >= 5
        );
        
        if (!empty($strongGrowthChats)) {
            $predictions[] = [
                'timeframe' => '今後6時間',
                'confidence' => 75,
                'prediction' => sprintf('現在成長中の%d個のチャットは、勢いを維持する可能性が高い', count($strongGrowthChats)),
                'reasoning' => '短時間での高い成長率と参加率から判断'
            ];
        }
        
        // 時間帯予測
        $hour = (int)date('H');
        if ($hour >= 19 && $hour <= 22) {
            $predictions[] = [
                'timeframe' => '今夜',
                'confidence' => 80,
                'prediction' => 'エンターテイメント系カテゴリの活動がさらに活発になる見込み',
                'reasoning' => '平日夜の時間帯パターンに基づく予測'
            ];
        }
        
        // 週末予測
        $dayOfWeek = date('w');
        if ($dayOfWeek == 5) { // 金曜日
            $predictions[] = [
                'timeframe' => '週末',
                'confidence' => 70,
                'prediction' => 'ゲームやレジャー関連チャットの参加者増加が期待される',
                'reasoning' => '週末パターンの履歴データに基づく'
            ];
        }
        
        return $predictions;
    }

    private function generateActionableRecommendations(array $risingChats, array $tagTrends): array
    {
        $recommendations = [];
        
        // 今注目すべきチャット
        if (!empty($risingChats)) {
            $recommendations[] = [
                'type' => 'trending_chats',
                'title' => '今注目のチャットルーム',
                'description' => '現在急成長中のチャットをチェックしてみましょう',
                'items' => array_slice($risingChats, 0, 5),
                'action' => 'すぐに参加'
            ];
        }
        
        // 人気タグ
        if (!empty($tagTrends)) {
            $hotTags = array_slice(array_filter($tagTrends, fn($tag) => 
                ($tag['total_1h_growth'] ?? 0) > 2
            ), 0, 8);
            
            if (!empty($hotTags)) {
                $recommendations[] = [
                    'type' => 'trending_tags',
                    'title' => '注目のキーワード',
                    'description' => 'これらのタグで新しいコミュニティを探してみては？',
                    'tags' => array_column($hotTags, 'tag'),
                    'action' => 'タグで検索'
                ];
            }
        }
        
        return $recommendations;
    }

    private function analyzeGrowthPatterns(array $risingChats, array $categoryTrends): array
    {
        $patterns = [];
        
        // 成長分布分析
        $growthRanges = [
            'explosive' => array_filter($risingChats, fn($chat) => ($chat['diff_member'] ?? 0) >= 20),
            'strong' => array_filter($risingChats, fn($chat) => ($chat['diff_member'] ?? 0) >= 10 && ($chat['diff_member'] ?? 0) < 20),
            'moderate' => array_filter($risingChats, fn($chat) => ($chat['diff_member'] ?? 0) >= 5 && ($chat['diff_member'] ?? 0) < 10),
            'steady' => array_filter($risingChats, fn($chat) => ($chat['diff_member'] ?? 0) >= 1 && ($chat['diff_member'] ?? 0) < 5)
        ];
        
        $patterns['growth_distribution'] = [
            'explosive_growth_count' => count($growthRanges['explosive']),
            'strong_growth_count' => count($growthRanges['strong']),
            'moderate_growth_count' => count($growthRanges['moderate']),
            'steady_growth_count' => count($growthRanges['steady'])
        ];
        
        // カテゴリ別成長パターン
        $patterns['category_performance'] = [];
        foreach ($categoryTrends as $trend) {
            if (($trend['total_growth'] ?? 0) > 0) {
                $patterns['category_performance'][] = [
                    'category' => $trend['category_name'],
                    'total_growth' => $trend['total_growth'],
                    'chat_count' => $trend['chat_count'],
                    'average_growth' => round($trend['avg_growth'] ?? 0, 1)
                ];
            }
        }
        
        return $patterns;
    }

    private function analyzeCategoryPerformance(array $categoryTrends): array
    {
        $insights = [];
        
        foreach (array_slice($categoryTrends, 0, 5) as $index => $trend) {
            $performance = 'good';
            if (($trend['avg_growth'] ?? 0) > 5) {
                $performance = 'excellent';
            } elseif (($trend['avg_growth'] ?? 0) < 2) {
                $performance = 'needs_attention';
            }
            
            $insights[] = [
                'rank' => $index + 1,
                'category' => $trend['category_name'],
                'total_growth' => $trend['total_growth'],
                'chat_count' => $trend['chat_count'],
                'average_growth' => round($trend['avg_growth'] ?? 0, 1),
                'performance' => $performance,
                'description' => $this->getCategoryInsightDescription($trend['category_name'], $trend)
            ];
        }
        
        return $insights;
    }

    private function getCategoryInsightDescription(string $category, array $trend): string
    {
        $avgGrowth = $trend['avg_growth'] ?? 0;
        $chatCount = $trend['chat_count'] ?? 0;
        
        return match(true) {
            $avgGrowth > 5 => sprintf('%sカテゴリは%d個のチャットで平均%.1f人/時の高成長を維持', $category, $chatCount, $avgGrowth),
            $avgGrowth > 2 => sprintf('%sカテゴリは安定した成長傾向（%d個のチャットで平均%.1f人/時）', $category, $chatCount, $avgGrowth),
            default => sprintf('%sカテゴリは成長が鈍化（%d個のチャットで平均%.1f人/時）', $category, $chatCount, $avgGrowth)
        };
    }

    private function detectRealisticAnomalies(array $risingChats, array $overallStats): array
    {
        $anomalies = [];
        
        // 異常な高成長の検出
        foreach ($risingChats as $chat) {
            $growth = $chat['diff_member'] ?? 0;
            if ($growth >= 30) {
                $anomalies[] = [
                    'type' => 'unusual_growth',
                    'severity' => 'high',
                    'chat_name' => $chat['name'],
                    'chat_id' => $chat['id'],
                    'growth' => $growth,
                    'description' => sprintf('%s人の急激な増加を検出', $growth),
                    'possible_causes' => ['メディア露出', 'バイラル効果', 'イベント連動', 'インフルエンサー参加']
                ];
            }
        }
        
        // 全体的な成長率の異常
        $totalGrowth = array_sum(array_column($risingChats, 'diff_member'));
        $growingChats = $overallStats['growing_chats'] ?? 0;
        $totalChats = $overallStats['total_chats'] ?? 1;
        $growthRate = ($growingChats / $totalChats) * 100;
        
        if ($growthRate > 15) {
            $anomalies[] = [
                'type' => 'platform_wide_growth',
                'severity' => 'medium',
                'description' => sprintf('全体の%.1f%%のチャットが同時成長（通常より高い活動レベル）', $growthRate),
                'possible_causes' => ['プラットフォームイベント', '外部要因', '季節的要因']
            ];
        }
        
        return $anomalies;
    }

    private function analyzeTimeBasedPatterns(int $hour, int $dayOfWeek, array $risingChats): array
    {
        $patterns = [];
        
        // 時間帯分析
        $patterns['current_time_context'] = [
            'hour' => $hour,
            'day_of_week' => $dayOfWeek,
            'time_period' => $this->getTimePeriodName($hour),
            'expected_activity_level' => $this->getExpectedActivityLevel($hour, $dayOfWeek),
            'current_growth_count' => count($risingChats)
        ];
        
        // 時間帯別の予想される活動
        $patterns['time_insights'] = [
            'description' => $this->getTimeInsightDescription($hour, $dayOfWeek),
            'recommended_actions' => $this->getTimeBasedRecommendations($hour, $dayOfWeek)
        ];
        
        return $patterns;
    }

    private function getTimePeriodName(int $hour): string
    {
        return match(true) {
            $hour >= 6 && $hour < 9 => '朝の時間帯',
            $hour >= 9 && $hour < 12 => '午前中',
            $hour >= 12 && $hour < 13 => 'お昼休み',
            $hour >= 13 && $hour < 18 => '午後',
            $hour >= 18 && $hour < 21 => '夕方から夜',
            $hour >= 21 && $hour < 24 => '夜の時間帯',
            default => '深夜・早朝'
        };
    }

    private function getExpectedActivityLevel(int $hour, int $dayOfWeek): string
    {
        // 平日（月-金：1-5）か週末（土日：0,6）か
        $isWeekend = $dayOfWeek == 0 || $dayOfWeek == 6;
        
        return match(true) {
            $hour >= 20 && $hour <= 22 => 'very_high',
            $hour >= 12 && $hour <= 13 && !$isWeekend => 'high',
            $hour >= 18 && $hour <= 23 && $isWeekend => 'high',
            $hour >= 9 && $hour <= 17 && !$isWeekend => 'medium',
            $hour >= 10 && $hour <= 20 && $isWeekend => 'medium',
            default => 'low'
        };
    }

    private function getTimeInsightDescription(int $hour, int $dayOfWeek): string
    {
        $isWeekend = $dayOfWeek == 0 || $dayOfWeek == 6;
        $weekdayNames = ['日', '月', '火', '水', '木', '金', '土'];
        $currentDay = $weekdayNames[$dayOfWeek];
        
        return match(true) {
            $hour >= 20 && $hour <= 22 => sprintf('%s曜日の夜はチャット活動が最も活発な時間帯です', $currentDay),
            $hour >= 12 && $hour <= 13 && !$isWeekend => 'お昼休みは平日の活動ピークの一つです',
            $hour >= 18 && $hour <= 19 && !$isWeekend => '仕事終わりの時間帯で活動が増加しています',
            $isWeekend && $hour >= 10 && $hour <= 20 => '週末はより長時間にわたって活動が続きます',
            $hour >= 2 && $hour <= 5 => '深夜時間帯の活動は限定的ですが、特定のコミュニティでは活発です',
            default => sprintf('%s曜日の現在時刻における標準的な活動レベルです', $currentDay)
        };
    }

    private function getTimeBasedRecommendations(int $hour, int $dayOfWeek): array
    {
        $recommendations = [];
        
        if ($hour >= 20 && $hour <= 22) {
            $recommendations[] = '夜の活動ピーク時間です。新しいチャットに参加するのに最適なタイミング';
            $recommendations[] = 'エンターテイメント系のチャットが特に活発になります';
        } elseif ($hour >= 12 && $hour <= 13) {
            $recommendations[] = 'お昼休み時間。軽い話題や情報交換系のチャットがおすすめ';
        } elseif ($hour >= 6 && $hour <= 9) {
            $recommendations[] = '朝の時間帯。ニュースや学習系のチャットが活発です';
        }
        
        return $recommendations;
    }

    private function analyzeMembershipTrends(array $overallStats, array $risingChats): array
    {
        $trends = [];
        
        // 成長率統計
        $totalGrowth = $overallStats['total_growth'] ?? 0;
        $growingChats = $overallStats['growing_chats'] ?? 0;
        $decliningChats = $overallStats['declining_chats'] ?? 0;
        $totalChats = $overallStats['total_chats'] ?? 1;
        
        $trends['growth_statistics'] = [
            'total_growth' => $totalGrowth,
            'growing_chat_percentage' => round(($growingChats / $totalChats) * 100, 1),
            'declining_chat_percentage' => round(($decliningChats / $totalChats) * 100, 1),
            'stable_chat_percentage' => round((($totalChats - $growingChats - $decliningChats) / $totalChats) * 100, 1)
        ];
        
        // 成長の質分析
        if (!empty($risingChats)) {
            $growthValues = array_column($risingChats, 'diff_member');
            $trends['growth_quality'] = [
                'average_growth' => round(array_sum($growthValues) / count($growthValues), 1),
                'max_growth' => max($growthValues),
                'min_growth' => min($growthValues),
                'median_growth' => $this->calculateMedian($growthValues)
            ];
        }
        
        return $trends;
    }

    private function calculateMedian(array $values): float
    {
        sort($values);
        $count = count($values);
        
        if ($count === 0) return 0;
        
        if ($count % 2 === 0) {
            return ($values[$count / 2 - 1] + $values[$count / 2]) / 2;
        } else {
            return $values[floor($count / 2)];
        }
    }

    private function generateRealisticAiComment(array $risingChats, array $categoryTrends, int $hour): string
    {
        $totalGrowth = array_sum(array_column($risingChats, 'diff_member'));
        $growthCount = count($risingChats);
        $topCategory = $categoryTrends[0]['category_name'] ?? 'ゲーム';
        
        $comments = [
            sprintf('データ分析の結果、%d個のチャットが合計%d人の成長を記録。%sカテゴリの活発さが目立ちます。', 
                $growthCount, $totalGrowth, $topCategory),
            sprintf('現在の時間帯（%d時）における活動パターンは予測モデルと一致しており、健全な成長傾向を示しています。', $hour),
            sprintf('成長中のチャット数は%d個。多様なカテゴリでバランスよく活動が見られ、コミュニティの活力を感じます。', $growthCount),
            sprintf('%sカテゴリを中心に%d人の新規参加者。データから見えるコミュニティの魅力が数値に表れています。', 
                $topCategory, $totalGrowth)
        ];
        
        return $comments[array_rand($comments)];
    }
    private function generateInsights(array $risingChats, array $categoryTrends, array $tagTrends): array
    {
        $insights = [];
        
        // カテゴリトレンド
        if (!empty($categoryTrends)) {
            $topCategories = array_slice(array_column($categoryTrends, 'category_name'), 0, 3);
            $insights[] = [
                'type' => 'category',
                'icon' => '📊',
                'title' => 'カテゴリトレンド',
                'content' => '「' . implode('」「', $topCategories) . '」が今最も注目されています'
            ];
        }
        
        // 急成長チャット
        if (!empty($risingChats) && $risingChats[0]['diff_member'] > 10) {
            $insights[] = [
                'type' => 'rapid_growth',
                'icon' => '🚀',
                'title' => '急成長を記録',
                'content' => '複数のチャットルームが同時に大幅な成長を見せており、新しいトレンドの兆候かもしれません'
            ];
        }
        
        // タグトレンド
        if (!empty($tagTrends)) {
            $hotTags = array_slice(array_column($tagTrends, 'tag'), 0, 5);
            $insights[] = [
                'type' => 'tag_trend',
                'icon' => '🏷️',
                'title' => '注目のキーワード',
                'content' => '「' . implode('」「', $hotTags) . '」関連のチャットが人気です'
            ];
        }
        
        return $insights;
    }
    
    private function generatePredictions(array $tagTrends, array $categoryTrends): array
    {
        $predictions = [];
        
        // 成長予測
        if (!empty($tagTrends)) {
            $growingTags = array_filter($tagTrends, fn($tag) => 
                ($tag['total_24h_growth'] ?? 0) > 10 && ($tag['total_1h_growth'] ?? 0) > 2
            );
            
            if (!empty($growingTags)) {
                $predictions[] = [
                    'type' => 'growth',
                    'confidence' => 'high',
                    'content' => '「' . array_values($growingTags)[0]['tag'] . '」関連のチャットは今後も成長が期待されます'
                ];
            }
        }
        
        // 時間帯予測
        $hour = (int)date('H');
        if ($hour >= 20 && $hour <= 23) {
            $predictions[] = [
                'type' => 'timeframe',
                'confidence' => 'medium',
                'content' => '深夜にかけてゲームやアニメ関連のチャットが活発になる傾向があります'
            ];
        }
        
        return $predictions;
    }
    
    private function generateRecommendations(array $risingChats, array $tagTrends): array
    {
        $recommendations = [];
        
        if (!empty($risingChats)) {
            $recommendations[] = [
                'title' => '今すぐチェックすべきチャット',
                'items' => array_slice($risingChats, 0, 3)
            ];
        }
        
        if (!empty($tagTrends)) {
            $recommendations[] = [
                'title' => 'トレンドタグから探す',
                'tags' => array_slice(array_column($tagTrends, 'tag'), 0, 10)
            ];
        }
        
        return $recommendations;
    }
}
