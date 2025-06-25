<?php

declare(strict_types=1);

namespace App\Services\AiTrend\Repository;

use App\Models\Repositories\DB;

/**
 * 高度なAI分析用データリポジトリ
 * 独自の解析アルゴリズムを使用した多様なデータソースからの情報収集
 */
class AiTrendAnalysisRepository
{
    /**
     * 隠れたバイラル成長パターン分析（高度アルゴリズム）
     * 
     * 分析要素:
     * - 成長加速度パターン（時間軸での変化率）
     * - 成長の持続性指標
     * - カテゴリ内での相対的成長
     * - 異常成長検出（通常パターンからの逸脱）
     */
    public function getHiddenViralPatterns(int $limit = 20): array
    {
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.member as current_members,
                oc.category,
                oc.description,
                COALESCE(srh.diff_member, 0) as hour_growth,
                COALESCE(srd.diff_member, 0) as day_growth,
                COALESCE(srw.diff_member, 0) as week_growth,
                COALESCE(srh.percent_increase, 0) as hour_growth_rate,
                COALESCE(srd.percent_increase, 0) as day_growth_rate,
                COALESCE(srw.percent_increase, 0) as week_growth_rate,
                -- 成長加速度計算（時間軸での変化率）
                CASE 
                    WHEN COALESCE(srd.diff_member, 0) > 0 
                    THEN COALESCE(srh.diff_member, 0) / COALESCE(srd.diff_member, 1) * 24
                    ELSE 0 
                END as growth_acceleration,
                -- 成長の持続性指標
                CASE 
                    WHEN COALESCE(srw.diff_member, 0) > 0 AND COALESCE(srd.diff_member, 0) > 0 
                    THEN COALESCE(srd.diff_member, 0) / (COALESCE(srw.diff_member, 0) / 7.0)
                    ELSE 0 
                END as growth_sustainability,
                -- バイラル可能性スコア（独自計算）
                (
                    COALESCE(srh.diff_member, 0) * 0.5 +
                    COALESCE(srd.diff_member, 0) * 0.3 +
                    COALESCE(srw.diff_member, 0) * 0.2 +
                    COALESCE(srh.percent_increase, 0) * 2
                ) as viral_potential_score
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id  
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE 
                oc.member BETWEEN 50 AND 100000
                AND (
                    -- 異常成長パターン検出
                    COALESCE(srh.diff_member, 0) > (oc.member * 0.05) OR
                    -- 加速成長パターン
                    (COALESCE(srh.diff_member, 0) > 2 AND COALESCE(srd.diff_member, 0) > 5) OR
                    -- 持続成長パターン
                    (COALESCE(srw.diff_member, 0) > 10 AND COALESCE(srd.diff_member, 0) > 2)
                )
            ORDER BY viral_potential_score DESC, growth_acceleration DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * 低競争高成長セグメント発見（高度分析）
     * 
     * 分析要素:
     * - 市場集中度指標（HHI: ハーフィンダール指数）
     * - 成長機会指数
     * - 新規参入容易性
     * - 競争密度と成長ポテンシャルの相関
     */
    public function getLowCompetitionHighGrowthSegments(int $limit = 15): array
    {
        $query = "
            SELECT 
                oc.category,
                COUNT(*) as total_chats_in_category,
                COUNT(CASE WHEN srw.diff_member > 0 THEN 1 END) as growing_chats,
                COUNT(CASE WHEN oc.member >= 10000 THEN 1 END) as dominant_players,
                COUNT(CASE WHEN oc.member < 1000 THEN 1 END) as small_players,
                ROUND(AVG(COALESCE(srw.diff_member, 0)), 2) as avg_weekly_growth,
                ROUND(AVG(COALESCE(srd.diff_member, 0)), 2) as avg_daily_growth,
                ROUND(AVG(oc.member), 0) as avg_member_size,
                -- 市場集中度（上位チャットの支配度）
                ROUND(
                    (SELECT SUM(POW(oc2.member, 2)) 
                     FROM open_chat oc2 
                     WHERE oc2.category = oc.category) / 
                    POW((SELECT SUM(oc3.member) 
                         FROM open_chat oc3 
                         WHERE oc3.category = oc.category), 2) * 10000, 4
                ) as market_concentration_index,
                -- 成長機会指数（独自算出）
                ROUND(
                    (AVG(COALESCE(srw.diff_member, 0)) * 
                     COUNT(CASE WHEN srw.diff_member > 0 THEN 1 END) / COUNT(*)) *
                    (1 - COUNT(CASE WHEN oc.member >= 10000 THEN 1 END) / COUNT(*)) * 100, 2
                ) as growth_opportunity_index,
                -- 新規参入容易性スコア
                ROUND(
                    (COUNT(CASE WHEN oc.member < 1000 AND COALESCE(srw.diff_member, 0) > 0 THEN 1 END) / 
                     COUNT(*)) * 100, 2
                ) as entry_ease_score
            FROM open_chat oc
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            WHERE oc.category IS NOT NULL AND oc.category > 0
            GROUP BY oc.category
            HAVING 
                total_chats_in_category >= 30
                AND growing_chats > 0
                AND market_concentration_index < 0.3  -- 低集中度
                AND growth_opportunity_index > 5      -- 高成長機会
            ORDER BY growth_opportunity_index DESC, entry_ease_score DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * リアルタイム成長加速分析（高度解析）
     * 
     * 分析要素:
     * - 成長モメンタム（加速度）
     * - 成長の一貫性（変動係数）
     * - 相対的成長強度
     * - ブレイクアウト指標
     */
    public function getCurrentGrowthAcceleration(int $limit = 10): array
    {
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.member,
                oc.category,
                oc.description,
                COALESCE(srh.diff_member, 0) as current_hour_growth,
                COALESCE(srd.diff_member, 0) as today_growth,
                COALESCE(srw.diff_member, 0) as week_growth,
                COALESCE(srh.percent_increase, 0) as hour_growth_rate,
                COALESCE(srd.percent_increase, 0) as day_growth_rate,
                COALESCE(srw.percent_increase, 0) as week_growth_rate,
                -- 成長モメンタム（時間軸での加速度）
                CASE 
                    WHEN COALESCE(srd.diff_member, 0) > 0 
                    THEN ROUND(COALESCE(srh.diff_member, 0) * 24.0 / COALESCE(srd.diff_member, 1), 2)
                    ELSE 0 
                END as growth_momentum,
                -- 成長の一貫性（変動の少なさ）
                CASE 
                    WHEN COALESCE(srw.diff_member, 0) > 0 
                    THEN ROUND(COALESCE(srd.diff_member, 0) * 7.0 / COALESCE(srw.diff_member, 1), 2)
                    ELSE 0 
                END as growth_consistency,
                -- 相対的成長強度（サイズ対比）
                ROUND(COALESCE(srh.diff_member, 0) / (oc.member * 0.01), 2) as relative_growth_strength,
                -- ブレイクアウト指標（急激な変化）
                CASE 
                    WHEN COALESCE(srh.diff_member, 0) > 0 AND COALESCE(srd.diff_member, 0) > 0 
                    THEN ROUND(COALESCE(srh.diff_member, 0) / (COALESCE(srd.diff_member, 0) / 24.0), 2)
                    ELSE 0 
                END as breakout_indicator,
                -- 総合加速度スコア
                ROUND(
                    (COALESCE(srh.diff_member, 0) * 2.0 +
                     COALESCE(srd.diff_member, 0) * 1.5 +
                     COALESCE(srw.diff_member, 0) * 0.5 +
                     COALESCE(srh.percent_increase, 0) * 5.0) / 
                    (CASE WHEN oc.member > 0 THEN LOG(oc.member) ELSE 1 END), 2
                ) as acceleration_score
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE 
                oc.member BETWEEN 20 AND 500000
                AND (
                    -- 急激な成長
                    COALESCE(srh.diff_member, 0) >= 2 OR
                    -- 持続的な成長
                    (COALESCE(srd.diff_member, 0) > 5 AND COALESCE(srw.diff_member, 0) > 10) OR
                    -- 高い成長率
                    COALESCE(srh.percent_increase, 0) > 3.0
                )
            ORDER BY acceleration_score DESC, growth_momentum DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * 成長爆発直前指標（高度予測分析）
     * 
     * 分析要素:
     * - 成長の兆候パターン認識
     * - 臨界点接近指標
     * - 成長の質と持続性
     * - バイラル予備軍の特定
     */
    public function getPreViralIndicators(int $limit = 15): array
    {
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.member,
                oc.category,
                oc.description,
                COALESCE(srh.diff_member, 0) as hour_growth,
                COALESCE(srd.diff_member, 0) as day_growth,
                COALESCE(srw.diff_member, 0) as week_growth,
                COALESCE(srh.percent_increase, 0) as hour_growth_rate,
                COALESCE(srd.percent_increase, 0) as day_growth_rate,
                COALESCE(srw.percent_increase, 0) as week_growth_rate,
                -- 成長の兆候強度（早期警告）
                CASE 
                    WHEN COALESCE(srh.diff_member, 0) > 0 AND COALESCE(srd.diff_member, 0) > 0 
                    THEN ROUND(
                        (COALESCE(srh.diff_member, 0) / (COALESCE(srd.diff_member, 0) / 24.0)) * 
                        (COALESCE(srd.diff_member, 0) / (COALESCE(srw.diff_member, 0) / 7.0)) * 
                        (COALESCE(srh.percent_increase, 0) / 10.0), 2)
                    ELSE 0 
                END as early_signal_strength,
                -- 臨界サイズ接近度（バイラル閾値）
                CASE 
                    WHEN oc.member < 5000 
                    THEN ROUND((oc.member + COALESCE(srw.diff_member, 0)) / 5000.0 * 100, 2)
                    ELSE 100 
                END as critical_mass_proximity,
                -- 成長の質指標（安定性）
                CASE 
                    WHEN COALESCE(srw.diff_member, 0) > 0 
                    THEN ROUND(
                        (COALESCE(srd.diff_member, 0) * 7.0 / COALESCE(srw.diff_member, 1)) * 
                        (COALESCE(srh.diff_member, 0) * 24.0 / COALESCE(srd.diff_member, 1)) * 0.5, 2)
                    ELSE 0 
                END as growth_quality_index,
                -- バイラル可能性予測スコア
                ROUND(
                    (COALESCE(srh.diff_member, 0) * 3.0 +
                     COALESCE(srd.diff_member, 0) * 2.0 +
                     COALESCE(srw.diff_member, 0) * 1.0 +
                     COALESCE(srh.percent_increase, 0) * 8.0 +
                     (CASE WHEN oc.member < 3000 THEN 20 ELSE 0 END)) / 
                    (CASE WHEN oc.member > 0 THEN LOG(oc.member + 1) ELSE 1 END), 2
                ) as viral_potential_score,
                -- タイミング指標（成長の勢い）
                CASE 
                    WHEN COALESCE(srh.diff_member, 0) > COALESCE(srd.diff_member, 0) / 24.0 
                    THEN 'accelerating'
                    WHEN COALESCE(srd.diff_member, 0) > COALESCE(srw.diff_member, 0) / 7.0 
                    THEN 'building'
                    ELSE 'stable'
                END as growth_timing_pattern
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE 
                oc.member BETWEEN 30 AND 15000
                AND (
                    -- 早期成長兆候
                    (COALESCE(srh.diff_member, 0) > 1 AND COALESCE(srd.diff_member, 0) > 3) OR
                    -- 持続的な上昇トレンド
                    (COALESCE(srw.diff_member, 0) > 5 AND COALESCE(srd.diff_member, 0) > 1) OR
                    -- 高い成長率
                    COALESCE(srh.percent_increase, 0) > 2.0 OR
                    -- 小規模での急成長
                    (oc.member < 1000 AND COALESCE(srd.diff_member, 0) > oc.member * 0.1)
                )
            ORDER BY viral_potential_score DESC, early_signal_strength DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * 新規参入チャンス分析（戦略的機会発見）
     * 
     * 分析要素:
     * - 市場参入障壁の低さ
     * - 競合密度と成長余地
     * - ニッチ市場の発見
     * - 成功確率指標
     */
    public function getNewEntrantOpportunities(int $limit = 20): array
    {
        $query = "
            SELECT 
                oc.category,
                COUNT(*) as total_chats,
                COUNT(CASE WHEN oc.member >= 10000 THEN 1 END) as dominant_players,
                COUNT(CASE WHEN oc.member BETWEEN 1000 AND 9999 THEN 1 END) as mid_tier_players,
                COUNT(CASE WHEN oc.member < 1000 THEN 1 END) as small_players,
                COUNT(CASE WHEN oc.member < 1000 AND COALESCE(srw.diff_member, 0) > 0 THEN 1 END) as growing_small_players,
                ROUND(AVG(COALESCE(srw.diff_member, 0)), 2) as avg_weekly_growth,
                ROUND(AVG(COALESCE(srd.diff_member, 0)), 2) as avg_daily_growth,
                ROUND(AVG(oc.member), 0) as avg_member_size,
                MAX(oc.member) as largest_chat_size,
                -- 市場参入障壁スコア（低いほど参入しやすい）
                ROUND(
                    (COUNT(CASE WHEN oc.member >= 10000 THEN 1 END) * 10.0 / COUNT(*)) + 
                    (AVG(oc.member) / 10000.0 * 5) +
                    (CASE WHEN MAX(oc.member) > 50000 THEN 20 ELSE 0 END), 2
                ) as entry_barrier_score,
                -- 成長機会指数
                ROUND(
                    (AVG(COALESCE(srw.diff_member, 0)) * 
                     COUNT(CASE WHEN COALESCE(srw.diff_member, 0) > 0 THEN 1 END) / COUNT(*) * 
                     (1 - COUNT(CASE WHEN oc.member >= 10000 THEN 1 END) / COUNT(*))) * 100, 2
                ) as growth_opportunity_score,
                -- ニッチ市場ポテンシャル
                CASE 
                    WHEN COUNT(*) < 100 AND AVG(COALESCE(srw.diff_member, 0)) > 5 
                    THEN 'high_niche_potential'
                    WHEN COUNT(*) < 200 AND COUNT(CASE WHEN oc.member >= 10000 THEN 1 END) <= 3 
                    THEN 'medium_niche_potential'
                    ELSE 'standard_market'
                END as niche_potential,
                -- 成功確率指標（新規参入での成功見込み）
                ROUND(
                    (COUNT(CASE WHEN oc.member < 1000 AND COALESCE(srw.diff_member, 0) > 5 THEN 1 END) * 100.0 / 
                     COUNT(CASE WHEN oc.member < 1000 THEN 1 END)) * 
                    (1 - LEAST(COUNT(CASE WHEN oc.member >= 10000 THEN 1 END) / COUNT(*), 0.8)), 2
                ) as success_probability,
                -- 競争密度
                ROUND(COUNT(*) / (AVG(COALESCE(srw.diff_member, 0)) + 1), 2) as competition_density,
                -- 市場活性度
                ROUND(
                    COUNT(CASE WHEN COALESCE(srw.diff_member, 0) > 0 THEN 1 END) * 100.0 / COUNT(*), 2
                ) as market_activity_rate
            FROM open_chat oc
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            WHERE oc.category IS NOT NULL AND oc.category > 0
            GROUP BY oc.category
            HAVING 
                total_chats >= 15
                AND entry_barrier_score <= 25        -- 参入障壁が低い
                AND growth_opportunity_score > 3     -- 成長機会がある
                AND market_activity_rate > 10        -- 市場が活発
                AND success_probability > 5          -- 成功確率がある
            ORDER BY 
                growth_opportunity_score DESC, 
                success_probability DESC,
                entry_barrier_score ASC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * 追加の高度分析メソッド: トレンド予測・異常検出
     */
    
    /**
     * 急上昇トレンド予測分析
     * 機械学習的アプローチでの成長予測
     */
    public function getTrendPredictionAnalysis(int $limit = 10): array
    {
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.member,
                oc.category,
                oc.description,
                COALESCE(srh.diff_member, 0) as hour_growth,
                COALESCE(srd.diff_member, 0) as day_growth,
                COALESCE(srw.diff_member, 0) as week_growth,
                -- トレンド予測スコア（複合指標）
                ROUND(
                    (COALESCE(srh.diff_member, 0) * 3.0 +
                     COALESCE(srd.diff_member, 0) * 2.0 +
                     COALESCE(srw.diff_member, 0) * 1.0) *
                    (CASE 
                        WHEN oc.member < 1000 THEN 2.0
                        WHEN oc.member < 5000 THEN 1.5
                        ELSE 1.0 
                    END) *
                    (COALESCE(srh.percent_increase, 0) / 10.0 + 1), 2
                ) as trend_prediction_score,
                -- 成長パターン分類
                CASE 
                    WHEN COALESCE(srh.diff_member, 0) > COALESCE(srd.diff_member, 0) / 12 
                         AND COALESCE(srd.diff_member, 0) > COALESCE(srw.diff_member, 0) / 5
                    THEN 'exponential_growth'
                    WHEN COALESCE(srw.diff_member, 0) > 0 
                         AND COALESCE(srd.diff_member, 0) > 0 
                         AND COALESCE(srh.diff_member, 0) > 0
                    THEN 'steady_growth'
                    WHEN COALESCE(srh.diff_member, 0) > 3 
                         AND COALESCE(srd.diff_member, 0) <= 5
                    THEN 'spike_growth'
                    ELSE 'uncertain'
                END as growth_pattern
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE 
                oc.member BETWEEN 10 AND 50000
                AND (COALESCE(srh.diff_member, 0) > 0 OR 
                     COALESCE(srd.diff_member, 0) > 0 OR 
                     COALESCE(srw.diff_member, 0) > 0)
            ORDER BY trend_prediction_score DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * 異常成長パターン検出
     * 通常の成長パターンから逸脱した特異なケースを発見
     */
    public function getAnomalousGrowthPatterns(int $limit = 8): array
    {
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.member,
                oc.category,
                oc.description,
                COALESCE(srh.diff_member, 0) as hour_growth,
                COALESCE(srd.diff_member, 0) as day_growth,
                COALESCE(srw.diff_member, 0) as week_growth,
                COALESCE(srh.percent_increase, 0) as hour_growth_rate,
                -- 異常度スコア（統計的異常検出）
                ROUND(
                    ABS(COALESCE(srh.diff_member, 0) - 
                        (SELECT AVG(COALESCE(srh2.diff_member, 0)) 
                         FROM statistics_ranking_hour srh2 
                         JOIN open_chat oc2 ON srh2.open_chat_id = oc2.id 
                         WHERE oc2.category = oc.category)) /
                    (SELECT STDDEV(COALESCE(srh3.diff_member, 0)) + 1
                     FROM statistics_ranking_hour srh3 
                     JOIN open_chat oc3 ON srh3.open_chat_id = oc3.id 
                     WHERE oc3.category = oc.category), 2
                ) as anomaly_score,
                -- 異常パターンの種類
                CASE 
                    WHEN COALESCE(srh.diff_member, 0) > oc.member * 0.2 
                    THEN 'massive_spike'
                    WHEN COALESCE(srh.diff_member, 0) > 50 AND oc.member < 500 
                    THEN 'small_chat_explosion'
                    WHEN COALESCE(srh.percent_increase, 0) > 50 
                    THEN 'extreme_percentage_growth'
                    ELSE 'statistical_outlier'
                END as anomaly_type
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE 
                oc.member BETWEEN 5 AND 100000
                AND oc.category IS NOT NULL
                AND (
                    -- 異常な時間成長
                    COALESCE(srh.diff_member, 0) > oc.member * 0.1 OR
                    -- 異常な成長率
                    COALESCE(srh.percent_increase, 0) > 30 OR
                    -- 小規模チャットの爆発的成長
                    (oc.member < 1000 AND COALESCE(srh.diff_member, 0) > 20)
                )
            ORDER BY anomaly_score DESC, hour_growth DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }
}