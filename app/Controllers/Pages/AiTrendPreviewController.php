<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use Shared\MimimalCmsConfig;
use Shadow\Kernel\Reception;
use App\Models\Repositories\DB;

class AiTrendPreviewController
{
    public function __construct()
    {
    }

    public function index(Reception $reception)
    {
        // DB接続
        DB::connect();

        // 1. 急上昇中のオープンチャット（1時間で最も増加）
        $risingChatsQuery = "
            SELECT 
                oc.id,
                oc.name,
                oc.member,
                oc.description,
                oc.img_url,
                srh.diff_member,
                srh.percent_increase
            FROM statistics_ranking_hour srh
            JOIN open_chat oc ON srh.open_chat_id = oc.id
            WHERE srh.diff_member > 0
            ORDER BY srh.diff_member DESC
            LIMIT 10
        ";
        
        $stmt = DB::$pdo->prepare($risingChatsQuery);
        $stmt->execute();
        $risingChats = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 2. カテゴリ別の増減集計
        $categoryTrendsQuery = "
            SELECT 
                oc.category,
                COUNT(DISTINCT oc.id) as chat_count,
                SUM(srh.diff_member) as total_growth,
                AVG(srh.diff_member) as avg_growth
            FROM statistics_ranking_hour srh
            JOIN open_chat oc ON srh.open_chat_id = oc.id
            WHERE oc.category IS NOT NULL
            GROUP BY oc.category
            ORDER BY total_growth DESC
        ";
        
        $stmt = DB::$pdo->prepare($categoryTrendsQuery);
        $stmt->execute();
        $categoryTrends = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // カテゴリ名をマッピング
        $categoryMap = array_flip(AppConfig::OPEN_CHAT_CATEGORY[MimimalCmsConfig::$urlRoot]);
        foreach ($categoryTrends as &$trend) {
            $trend['category_name'] = $categoryMap[$trend['category']] ?? 'その他';
        }

        // 3. 人気タグのトレンド
        $tagTrendsQuery = "
            SELECT 
                r.tag,
                COUNT(DISTINCT oc.id) as room_count,
                SUM(srh.diff_member) as total_1h_growth,
                SUM(srh24.diff_member) as total_24h_growth
            FROM recommend r
            JOIN open_chat oc ON r.id = oc.id
            JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            JOIN statistics_ranking_hour24 srh24 ON oc.id = srh24.open_chat_id
            WHERE r.tag != ''
            GROUP BY r.tag
            HAVING total_1h_growth > 0 OR total_24h_growth > 0
            ORDER BY total_1h_growth DESC
            LIMIT 20
        ";
        
        $stmt = DB::$pdo->prepare($tagTrendsQuery);
        $stmt->execute();
        $tagTrends = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 4. 全体統計
        $overallStatsQuery = "
            SELECT 
                COUNT(DISTINCT oc.id) as total_chats,
                SUM(oc.member) as total_members,
                SUM(CASE WHEN srh.diff_member > 0 THEN 1 ELSE 0 END) as growing_chats,
                SUM(CASE WHEN srh.diff_member < 0 THEN 1 ELSE 0 END) as declining_chats,
                SUM(srh.diff_member) as total_growth
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
        ";
        
        $stmt = DB::$pdo->prepare($overallStatsQuery);
        $stmt->execute();
        $overallStats = $stmt->fetch(\PDO::FETCH_ASSOC);

        // AIトレンド分析（モックデータ）
        $aiAnalysis = $this->generateAiAnalysis($risingChats, $categoryTrends, $tagTrends);

        // メタデータ設定
        $_meta = meta();
        $_meta->title = 'AIトレンド分析プレビュー | オプチャグラフ';
        $_meta->description = 'LINEオープンチャットのAIによるトレンド分析';

        return view('ai_trend_preview', [
            'risingChats' => $risingChats,
            'categoryTrends' => $categoryTrends,
            'tagTrends' => $tagTrends,
            'overallStats' => $overallStats,
            'aiAnalysis' => $aiAnalysis,
            '_meta' => $_meta,
        ]);
    }

    private function generateAiAnalysis(array $risingChats, array $categoryTrends, array $tagTrends): array
    {
        // 現在の時刻
        $hour = (int)date('H');
        $dayOfWeek = date('w');
        
        // 曜日名
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $currentWeekday = $weekdays[$dayOfWeek];
        
        // トレンド分析のモックデータ生成
        $analysis = [
            'summary' => $this->generateSummary($hour, $currentWeekday, $risingChats, $categoryTrends),
            'insights' => $this->generateInsights($risingChats, $categoryTrends, $tagTrends),
            'predictions' => $this->generatePredictions($tagTrends, $categoryTrends),
            'recommendations' => $this->generateRecommendations($risingChats, $tagTrends),
        ];
        
        return $analysis;
    }
    
    private function generateSummary(int $hour, string $weekday, array $risingChats, array $categoryTrends): string
    {
        $topCategory = $categoryTrends[0]['category_name'] ?? 'ゲーム';
        $topChat = $risingChats[0]['name'] ?? '';
        $topGrowth = $risingChats[0]['diff_member'] ?? 0;
        
        $timeContext = match(true) {
            $hour >= 6 && $hour < 9 => '朝の通勤時間帯',
            $hour >= 12 && $hour < 13 => 'お昼休み',
            $hour >= 18 && $hour < 21 => '夕方から夜にかけて',
            $hour >= 21 && $hour < 24 => '深夜',
            default => '現在'
        };
        
        return "{$weekday}曜日の{$timeContext}、「{$topCategory}」カテゴリが最も活発です。特に「{$topChat}」が{$topGrowth}人増加と急成長を見せています。";
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