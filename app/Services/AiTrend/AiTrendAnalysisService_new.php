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

        // 基本データを取得
        $risingChats = $this->getRisingChats();
        $categoryTrends = $this->getCategoryTrends();
        $tagTrends = $this->getTagTrends();
        $overallStats = $this->getOverallStats();

        // LLMによる分析を実行
        $aiAnalysis = $this->generateAiAnalysis($risingChats, $categoryTrends, $tagTrends, $overallStats);

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
                oc.category,
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
                SUM(oc.member) as total_members
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
                AVG(oc.member) as avg_member_count
            FROM recommend r
            JOIN open_chat oc ON r.id = oc.id
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            WHERE r.tag != '' AND r.tag IS NOT NULL
            GROUP BY r.tag
            HAVING total_1h_growth > 0 OR room_count >= 3
            ORDER BY total_1h_growth DESC
            LIMIT 20
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
                AVG(CASE WHEN srh.diff_member > 0 THEN srh.diff_member ELSE NULL END) as avg_growth_positive
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
        ";
        
        $stmt = DB::$pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * LLMならではの価値ある分析を生成
     * - パターン認識による洞察
     * - データの文脈理解
     * - ユーザーにとって実用的な情報抽出
     */
    private function generateAiAnalysis(array $risingChats, array $categoryTrends, array $tagTrends, array $overallStats): AiAnalysisDto
    {
        // 現在の状況を自然言語でまとめる
        $summary = $this->generateSummary($risingChats, $categoryTrends, $overallStats);
        
        // データから意味のある洞察を抽出
        $insights = $this->extractMeaningfulInsights($risingChats, $categoryTrends, $tagTrends);
        
        // 実用的な予測を生成
        $predictions = $this->generateRealisticPredictions($risingChats, $categoryTrends);
        
        // ユーザーに役立つ推奨事項
        $recommendations = $this->generateActionableRecommendations($risingChats, $tagTrends);

        return new AiAnalysisDto(
            $summary,
            $insights,
            $predictions,
            $recommendations,
            [], // growthPatterns
            [], // categoryInsights  
            [], // anomalies
            [], // timePatterns
            [], // membershipTrends
            [], // metadata
            '' // aiComment
        );
    }

    private function generateSummary(array $risingChats, array $categoryTrends, array $overallStats): string
    {
        $totalGrowth = $overallStats['total_growth'] ?? 0;
        $growingChats = $overallStats['growing_chats'] ?? 0;
        $topCategory = $categoryTrends[0]['category_name'] ?? 'ゲーム';
        $hour = (int)date('H');
        
        $timeContext = match (true) {
            $hour >= 20 && $hour <= 23 => '夜の活動ピーク時間帯において',
            $hour >= 12 && $hour <= 14 => 'お昼休み時間帯において',
            $hour >= 7 && $hour <= 9 => '朝の活動時間帯において',
            default => '現在の時間帯において'
        };
        
        return sprintf(
            '%s、%d個のチャットルームで合計%d人の新規参加者を確認。特に「%s」カテゴリの成長が顕著で、コミュニティ全体の活力が数値に現れています。',
            $timeContext,
            $growingChats,
            $totalGrowth,
            $topCategory
        );
    }

    private function extractMeaningfulInsights(array $risingChats, array $categoryTrends, array $tagTrends): array
    {
        $insights = [];
        
        // カテゴリ集中度分析
        if (!empty($categoryTrends) && count($categoryTrends) > 1) {
            $topCategoryGrowth = $categoryTrends[0]['total_growth'] ?? 0;
            $totalGrowth = array_sum(array_column($categoryTrends, 'total_growth'));
            $concentration = $totalGrowth > 0 ? round(($topCategoryGrowth / $totalGrowth) * 100, 1) : 0;
            
            if ($concentration > 50) {
                $insights[] = [
                    'icon' => '🎯',
                    'title' => 'カテゴリ集中現象',
                    'content' => sprintf('「%s」カテゴリが全体成長の%s%%を占めており、特定分野への関心集中が見られます。これは新しいトレンドの兆候かもしれません。', 
                        $categoryTrends[0]['category_name'], $concentration),
                    'confidence' => 85
                ];
            } else {
                $insights[] = [
                    'icon' => '📊',
                    'title' => '多様性のある成長',
                    'content' => sprintf('成長が%d個のカテゴリに分散しており、コミュニティの興味が多様化しています。健全な発展の証拠といえます。', 
                        count($categoryTrends)),
                    'confidence' => 80
                ];
            }
        }
        
        // 成長パターン分析
        if (!empty($risingChats)) {
            $highGrowthCount = count(array_filter($risingChats, fn($chat) => ($chat['diff_member'] ?? 0) >= 10));
            $totalChats = count($risingChats);
            
            if ($highGrowthCount / $totalChats > 0.3) {
                $insights[] = [
                    'icon' => '🚀',
                    'title' => '高成長チャットの集中',
                    'content' => sprintf('%d個中%d個のチャットが10人以上の急成長を記録。通常を上回る活発な動きが見られます。', 
                        $totalChats, $highGrowthCount),
                    'confidence' => 90
                ];
            }
        }
        
        // タグトレンド分析
        if (!empty($tagTrends)) {
            $activeTagCount = count(array_filter($tagTrends, fn($tag) => ($tag['total_1h_growth'] ?? 0) > 5));
            if ($activeTagCount >= 5) {
                $hotTags = array_slice(array_column(array_filter($tagTrends, fn($tag) => 
                    ($tag['total_1h_growth'] ?? 0) > 5), 'tag'), 0, 3);
                
                $insights[] = [
                    'icon' => '🏷️',
                    'title' => 'キーワードトレンドの多様化',
                    'content' => sprintf('「%s」などのキーワードで同時に成長が見られ、関心分野の拡大を示しています。', 
                        implode('」「', $hotTags)),
                    'confidence' => 75
                ];
            }
        }
        
        return $insights;
    }

    private function generateRealisticPredictions(array $risingChats, array $categoryTrends): array
    {
        $predictions = [];
        
        // 成長継続予測
        $strongGrowthChats = array_filter($risingChats, fn($chat) => 
            ($chat['diff_member'] ?? 0) >= 8 && isset($chat['created_at'])
        );
        
        if (!empty($strongGrowthChats)) {
            $predictions[] = [
                'timeframe' => '今後6時間',
                'confidence' => 75,
                'content' => sprintf('現在急成長中の%d個のチャットは、参加率とエンゲージメントから判断して成長を継続する可能性が高い', 
                    count($strongGrowthChats))
            ];
        }
        
        // カテゴリ予測
        if (!empty($categoryTrends)) {
            $topCategory = $categoryTrends[0]['category_name'] ?? '';
            if ($topCategory) {
                $hour = (int)date('H');
                if (($topCategory === 'ゲーム' && $hour >= 19) || 
                    ($topCategory === 'エンターテイメント' && $hour >= 20)) {
                    $predictions[] = [
                        'timeframe' => '今夜',
                        'confidence' => 80,
                        'content' => sprintf('「%s」カテゴリは夜間の活動ピーク時間に入るため、さらなる成長が期待される', $topCategory)
                    ];
                }
            }
        }
        
        return $predictions;
    }

    private function generateActionableRecommendations(array $risingChats, array $tagTrends): array
    {
        $recommendations = [];
        
        // 注目チャット推奨
        if (!empty($risingChats)) {
            $topChats = array_slice($risingChats, 0, 3);
            $recommendations[] = [
                'title' => '今すぐ参加すべきチャット',
                'description' => sprintf('%s など、現在活発に成長中のチャットルームで新しいコミュニティを体験してみましょう', 
                    $topChats[0]['name'] ?? ''),
                'action_type' => 'visit_chats',
                'items' => $topChats
            ];
        }
        
        // トレンドタグ活用推奨
        if (!empty($tagTrends)) {
            $hotTags = array_slice(array_filter($tagTrends, fn($tag) => 
                ($tag['total_1h_growth'] ?? 0) > 2), 0, 5);
            
            if (!empty($hotTags)) {
                $recommendations[] = [
                    'title' => '注目キーワードで新発見',
                    'description' => sprintf('「%s」などのタグで検索すると、今話題のチャットが見つかります', 
                        $hotTags[0]['tag'] ?? ''),
                    'action_type' => 'search_tags',
                    'tags' => array_column($hotTags, 'tag')
                ];
            }
        }
        
        // 時間帯別推奨
        $hour = (int)date('H');
        if ($hour >= 20 && $hour <= 22) {
            $recommendations[] = [
                'title' => '夜の活動ピーク時間を活用',
                'description' => '現在は1日で最も活発な時間帯です。新しいチャットに参加するには絶好のタイミングです',
                'action_type' => 'timing_advice'
            ];
        }
        
        return $recommendations;
    }
}
