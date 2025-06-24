<?php

declare(strict_types=1);

namespace App\Services\AiTrend\Repository;

use App\Models\Repositories\DB;

/**
 * AI分析用データリポジトリ（簡素化版）
 */
class AiTrendAnalysisRepository
{
    /**
     * 隠れたバイラル成長パターン分析（簡素化）
     */
    public function getHiddenViralPatterns(int $limit = 20): array
    {
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.member as current_members,
                oc.category,
                COALESCE(srh.diff_member, 0) as hour_growth,
                COALESCE(srd.diff_member, 0) as day_growth,
                COALESCE(srw.diff_member, 0) as week_growth
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id  
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE 
                oc.member BETWEEN 100 AND 50000
                AND COALESCE(srh.diff_member, 0) > 3
                AND COALESCE(srw.diff_member, 0) > 0
            ORDER BY srh.diff_member DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * 低競争高成長セグメント発見（簡素化）
     */
    public function getLowCompetitionHighGrowthSegments(int $limit = 15): array
    {
        $query = "
            SELECT 
                oc.category,
                COUNT(*) as total_chats_in_category,
                COUNT(CASE WHEN srw.diff_member > 0 THEN 1 END) as growing_chats,
                ROUND(AVG(COALESCE(srw.diff_member, 0)), 2) as avg_weekly_growth
            FROM open_chat oc
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE oc.category IS NOT NULL
            GROUP BY oc.category
            HAVING total_chats_in_category >= 50
            ORDER BY avg_weekly_growth DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * リアルタイム成長加速分析（簡素化）
     */
    public function getCurrentGrowthAcceleration(int $limit = 10): array
    {
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.member,
                oc.category,
                COALESCE(srh.diff_member, 0) as current_hour_growth,
                COALESCE(srd.diff_member, 0) as today_growth,
                COALESCE(srw.diff_member, 0) as week_growth
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE 
                COALESCE(srh.diff_member, 0) >= 3
                AND oc.member BETWEEN 50 AND 100000
                AND COALESCE(srw.diff_member, 0) > 0
            ORDER BY srh.diff_member DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * 成長爆発直前指標（簡素化）
     */
    public function getPreViralIndicators(int $limit = 15): array
    {
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.member,
                oc.category,
                COALESCE(srh.diff_member, 0) as hour_growth,
                COALESCE(srd.diff_member, 0) as day_growth,
                COALESCE(srw.diff_member, 0) as week_growth
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE 
                oc.member BETWEEN 50 AND 10000
                AND COALESCE(srw.diff_member, 0) > 0
                AND COALESCE(srd.diff_member, 0) > 0
                AND COALESCE(srh.diff_member, 0) > 0
            ORDER BY 
                (COALESCE(srh.diff_member, 0) + COALESCE(srd.diff_member, 0) + COALESCE(srw.diff_member, 0)) DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * 新規参入チャンス分析（簡素化）
     */
    public function getNewEntrantOpportunities(int $limit = 20): array
    {
        $query = "
            SELECT 
                oc.category,
                COUNT(*) as total_chats,
                COUNT(CASE WHEN oc.member >= 10000 THEN 1 END) as dominant_players,
                COUNT(CASE WHEN oc.member < 1000 AND COALESCE(srw.diff_member, 0) > 0 THEN 1 END) as growing_small_players,
                ROUND(AVG(COALESCE(srw.diff_member, 0)), 2) as avg_growth_rate
            FROM open_chat oc
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE oc.category IS NOT NULL
            GROUP BY oc.category
            HAVING 
                total_chats >= 20
                AND dominant_players <= 5
                AND growing_small_players > 0
            ORDER BY avg_growth_rate DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }
}