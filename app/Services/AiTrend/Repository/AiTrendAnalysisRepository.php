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
                LEFT(oc.description, 30) as description,
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
                LEFT(oc.description, 30) as description,
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
                LEFT(oc.description, 30) as description,
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
                LEFT(oc.description, 30) as description,
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
                LEFT(oc.description, 30) as description,
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

    /**
     * 成長勢い急上昇分析（新規追加）
     * 短期間で急激に成長勢いが増している実例を発見
     */
    public function getMomentumSurgeAnalysis(int $limit = 8): array
    {
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.member,
                oc.category,
                LEFT(oc.description, 30) as description,
                COALESCE(srh.diff_member, 0) as hour_growth,
                COALESCE(srd.diff_member, 0) as day_growth,
                COALESCE(srw.diff_member, 0) as week_growth,
                COALESCE(srh.percent_increase, 0) as hour_growth_rate,
                -- 勢い急上昇指標（時間軸での加速度変化）
                CASE 
                    WHEN COALESCE(srd.diff_member, 0) > 0 AND COALESCE(srw.diff_member, 0) > 0
                    THEN ROUND(
                        (COALESCE(srh.diff_member, 0) * 24.0 / COALESCE(srd.diff_member, 1)) /
                        (COALESCE(srd.diff_member, 0) * 7.0 / COALESCE(srw.diff_member, 1)), 3
                    )
                    ELSE 0 
                END as momentum_acceleration_ratio,
                -- 勢い持続性指標
                CASE 
                    WHEN COALESCE(srh.diff_member, 0) > 0 AND COALESCE(srd.diff_member, 0) > 0
                    THEN ROUND(COALESCE(srh.diff_member, 0) / (COALESCE(srd.diff_member, 0) / 24.0), 2)
                    ELSE 0 
                END as momentum_sustainability,
                -- 勢い急上昇スコア
                ROUND(
                    (COALESCE(srh.diff_member, 0) * 4.0 +
                     COALESCE(srd.diff_member, 0) * 2.0 +
                     COALESCE(srh.percent_increase, 0) * 6.0) *
                    CASE 
                        WHEN oc.member < 1000 THEN 2.5
                        WHEN oc.member < 5000 THEN 1.8
                        ELSE 1.2 
                    END, 2
                ) as momentum_surge_score
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE 
                oc.member BETWEEN 20 AND 30000
                AND COALESCE(srh.diff_member, 0) > 1
                AND COALESCE(srd.diff_member, 0) > 2
                AND (
                    -- 急激な勢い増加
                    COALESCE(srh.diff_member, 0) > COALESCE(srd.diff_member, 0) / 12 OR
                    -- 高い成長率
                    COALESCE(srh.percent_increase, 0) > 5.0 OR
                    -- 小規模チャットの急成長
                    (oc.member < 2000 AND COALESCE(srh.diff_member, 0) > 3)
                )
            ORDER BY momentum_surge_score DESC, momentum_acceleration_ratio DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * 隠れた優良株発見（新規追加）
     * ランキング外だが高い潜在能力を持つチャットを発見
     */
    public function getHiddenGemAnalysis(int $limit = 10): array
    {
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.member,
                oc.category,
                LEFT(oc.description, 30) as description,
                COALESCE(srh.diff_member, 0) as hour_growth,
                COALESCE(srd.diff_member, 0) as day_growth,
                COALESCE(srw.diff_member, 0) as week_growth,
                COALESCE(srh.percent_increase, 0) as hour_growth_rate,
                -- 隠れた価値指標（規模対比での成長力）
                ROUND(
                    (COALESCE(srw.diff_member, 0) * 7.0 + 
                     COALESCE(srd.diff_member, 0) * 3.0 + 
                     COALESCE(srh.diff_member, 0) * 2.0) / 
                    (oc.member * 0.01 + 1), 2
                ) as relative_growth_power,
                -- 将来性指標（小規模での高成長）
                CASE 
                    WHEN oc.member < 500 AND COALESCE(srw.diff_member, 0) > 5
                    THEN ROUND(COALESCE(srw.diff_member, 0) * 100.0 / oc.member, 2)
                    WHEN oc.member < 2000 AND COALESCE(srw.diff_member, 0) > 10
                    THEN ROUND(COALESCE(srw.diff_member, 0) * 50.0 / oc.member, 2)
                    ELSE 0 
                END as future_potential_ratio,
                -- 隠れた優良株スコア
                ROUND(
                    (COALESCE(srw.diff_member, 0) * 2.0 +
                     COALESCE(srd.diff_member, 0) * 1.5 +
                     COALESCE(srh.diff_member, 0) * 1.0) *
                    (CASE 
                        WHEN oc.member < 100 THEN 10.0
                        WHEN oc.member < 500 THEN 5.0
                        WHEN oc.member < 2000 THEN 2.5
                        ELSE 1.0 
                    END) +
                    COALESCE(srh.percent_increase, 0), 2
                ) as hidden_gem_score
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE 
                oc.member BETWEEN 10 AND 3000  -- 小規模～中規模
                AND (
                    -- 安定した成長
                    (COALESCE(srw.diff_member, 0) > 3 AND COALESCE(srd.diff_member, 0) > 0) OR
                    -- 高い成長率
                    COALESCE(srh.percent_increase, 0) > 3.0 OR
                    -- 継続的な増加
                    (COALESCE(srh.diff_member, 0) > 0 AND COALESCE(srd.diff_member, 0) > 1)
                )
            ORDER BY hidden_gem_score DESC, relative_growth_power DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * ブレイクタイミング分析（新規追加）
     * 成長の臨界点に到達しつつあるチャットを特定
     */
    public function getBreakthroughTimingAnalysis(int $limit = 12): array
    {
        $query = "
            SELECT 
                oc.id,
                oc.name,
                oc.member,
                oc.category,
                LEFT(oc.description, 30) as description,
                COALESCE(srh.diff_member, 0) as hour_growth,
                COALESCE(srd.diff_member, 0) as day_growth,
                COALESCE(srw.diff_member, 0) as week_growth,
                COALESCE(srh.percent_increase, 0) as hour_growth_rate,
                -- 臨界点接近度（特定の規模での急成長）
                CASE 
                    WHEN oc.member BETWEEN 800 AND 1200 THEN 'approaching_1k'
                    WHEN oc.member BETWEEN 4000 AND 6000 THEN 'approaching_5k'
                    WHEN oc.member BETWEEN 9000 AND 11000 THEN 'approaching_10k'
                    WHEN oc.member BETWEEN 45000 AND 55000 THEN 'approaching_50k'
                    ELSE 'other'
                END as critical_threshold_status,
                -- ブレイクタイミング指標
                ROUND(
                    (COALESCE(srh.diff_member, 0) * 3.0 +
                     COALESCE(srd.diff_member, 0) * 2.0 +
                     COALESCE(srw.diff_member, 0) * 1.0) *
                    CASE 
                        WHEN oc.member BETWEEN 800 AND 1200 THEN 3.0
                        WHEN oc.member BETWEEN 4000 AND 6000 THEN 2.5
                        WHEN oc.member BETWEEN 9000 AND 11000 THEN 2.0
                        WHEN oc.member BETWEEN 45000 AND 55000 THEN 1.5
                        ELSE 1.0 
                    END, 2
                ) as breakthrough_timing_score,
                -- 成長の一貫性
                CASE 
                    WHEN COALESCE(srh.diff_member, 0) > 0 AND 
                         COALESCE(srd.diff_member, 0) > 0 AND 
                         COALESCE(srw.diff_member, 0) > 0
                    THEN 'consistent_growth'
                    WHEN COALESCE(srh.diff_member, 0) > COALESCE(srd.diff_member, 0) / 12
                    THEN 'accelerating'
                    ELSE 'irregular'
                END as growth_consistency_pattern
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE 
                (
                    -- 1k突破間近
                    (oc.member BETWEEN 800 AND 1200 AND COALESCE(srd.diff_member, 0) > 1) OR
                    -- 5k突破間近
                    (oc.member BETWEEN 4000 AND 6000 AND COALESCE(srd.diff_member, 0) > 3) OR
                    -- 10k突破間近
                    (oc.member BETWEEN 9000 AND 11000 AND COALESCE(srd.diff_member, 0) > 5) OR
                    -- 50k突破間近
                    (oc.member BETWEEN 45000 AND 55000 AND COALESCE(srd.diff_member, 0) > 10)
                )
                AND COALESCE(srh.diff_member, 0) >= 0  -- 現在減少していない
            ORDER BY breakthrough_timing_score DESC, hour_growth DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * AI選出用の統合候補チャット取得
     * 複数の高度な分析結果を統合し、重複を排除して多様な候補を返す
     * 
     * @param int $limit 各分析手法から取得する候補数
     * @return array
     */
    public function getIntegratedCandidatesForAiSelection(int $limit = 15): array
    {
        // 複数の分析手法から候補を収集
        $candidates = [];
        $seenIds = [];

        // 1. 隠れたバイラルパターン分析
        $viralPatterns = $this->getHiddenViralPatterns($limit);
        foreach ($viralPatterns as $chat) {
            if (!isset($seenIds[$chat['id']])) {
                $chat['selection_source'] = 'viral_pattern';
                $chat['analysis_reason'] = '急速な成長加速度と高い持続性を持つ隠れたバイラル候補';
                $candidates[] = $chat;
                $seenIds[$chat['id']] = true;
            }
        }

        // 2. 成長爆発直前指標
        $preViralChats = $this->getPreViralIndicators($limit);
        foreach ($preViralChats as $chat) {
            if (!isset($seenIds[$chat['id']])) {
                $chat['selection_source'] = 'pre_viral';
                $chat['analysis_reason'] = '成長の兆候が強く、バイラル爆発の臨界点に接近中';
                $candidates[] = $chat;
                $seenIds[$chat['id']] = true;
            }
        }

        // 3. リアルタイム成長加速
        $acceleratingChats = $this->getCurrentGrowthAcceleration($limit);
        foreach ($acceleratingChats as $chat) {
            if (!isset($seenIds[$chat['id']])) {
                $chat['selection_source'] = 'real_time_acceleration';
                $chat['analysis_reason'] = 'リアルタイムで急激な成長加速を記録中';
                $candidates[] = $chat;
                $seenIds[$chat['id']] = true;
            }
        }

        // 4. 異常成長パターン
        $anomalousChats = $this->getAnomalousGrowthPatterns($limit);
        foreach ($anomalousChats as $chat) {
            if (!isset($seenIds[$chat['id']])) {
                $chat['selection_source'] = 'anomaly';
                $chat['analysis_reason'] = '統計的に異常な成長パターンを示す特異なケース';
                $candidates[] = $chat;
                $seenIds[$chat['id']] = true;
            }
        }

        // 5. トレンド予測分析（高スコア）
        $trendPredictions = $this->getTrendPredictionAnalysis($limit);
        foreach ($trendPredictions as $chat) {
            if (!isset($seenIds[$chat['id']])) {
                $chat['selection_source'] = 'trend_prediction';
                $chat['analysis_reason'] = '機械学習的アプローチで高い成長予測スコアを記録';
                $candidates[] = $chat;
                $seenIds[$chat['id']] = true;
            }
        }

        // 6. ニッチ市場の成長機会（カテゴリ単位）
        $lowCompetitionSegments = $this->getLowCompetitionHighGrowthSegments(10);
        foreach ($lowCompetitionSegments as $segment) {
            // 該当カテゴリの上位チャットを取得
            $categoryChatsQuery = "
                SELECT 
                    oc.id,
                    oc.name,
                    oc.member,
                    oc.category,
                    LEFT(oc.description, 30) as description,
                    COALESCE(srw.diff_member, 0) as week_growth,
                    COALESCE(srd.diff_member, 0) as day_growth,
                    COALESCE(srh.diff_member, 0) as hour_growth
                FROM open_chat oc
                LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
                LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
                LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
                WHERE oc.category = :category
                    AND oc.member < 5000
                    AND (COALESCE(srw.diff_member, 0) > 0 OR COALESCE(srd.diff_member, 0) > 0)
                ORDER BY 
                    (COALESCE(srw.diff_member, 0) * 0.5 + 
                     COALESCE(srd.diff_member, 0) * 1.5 + 
                     COALESCE(srh.diff_member, 0) * 2.0) DESC
                LIMIT 3
            ";

            $categoryChats = DB::fetchAll($categoryChatsQuery, ['category' => $segment['category']]);
            foreach ($categoryChats as $chat) {
                if (!isset($seenIds[$chat['id']])) {
                    $chat['selection_source'] = 'low_competition_segment';
                    $chat['analysis_reason'] = '低競争・高成長セグメントでの有望株';
                    $chat['market_opportunity_score'] = $segment['growth_opportunity_index'] ?? 0;
                    $candidates[] = $chat;
                    $seenIds[$chat['id']] = true;
                }
            }
        }

        // 新規追加分析手法
        // 7. 成長勢い急上昇分析
        $momentumSurgeChats = $this->getMomentumSurgeAnalysis($limit);
        foreach ($momentumSurgeChats as $chat) {
            if (!isset($seenIds[$chat['id']])) {
                $chat['selection_source'] = 'momentum_surge';
                $chat['analysis_reason'] = '短期間で急激に成長勢いが加速している注目株';
                $candidates[] = $chat;
                $seenIds[$chat['id']] = true;
            }
        }

        // 8. 隠れた優良株発見
        $hiddenGemChats = $this->getHiddenGemAnalysis($limit);
        foreach ($hiddenGemChats as $chat) {
            if (!isset($seenIds[$chat['id']])) {
                $chat['selection_source'] = 'hidden_gem';
                $chat['analysis_reason'] = 'ランキング外だが高い潜在能力を持つ隠れた優良株';
                $candidates[] = $chat;
                $seenIds[$chat['id']] = true;
            }
        }

        // 9. ブレイクタイミング分析
        $breakthroughChats = $this->getBreakthroughTimingAnalysis($limit);
        foreach ($breakthroughChats as $chat) {
            if (!isset($seenIds[$chat['id']])) {
                $chat['selection_source'] = 'breakthrough_timing';
                $chat['analysis_reason'] = '重要な成長の臨界点に到達しつつある絶好のタイミング';
                $candidates[] = $chat;
                $seenIds[$chat['id']] = true;
            }
        }

        // 新規追加: 将来成長性分析
        $futureGrowthChats = $this->getFutureGrowthPotentialAnalysis($limit);
        foreach ($futureGrowthChats as $chat) {
            if (!isset($seenIds[$chat['id']])) {
                $chat['selection_source'] = 'future_growth_potential';
                $chat['analysis_reason'] = '現在は安定しているが将来の成長ポテンシャルが極めて高い';
                $chat['future_potential_score'] = $chat['future_potential_score'] ?? 0;
                $candidates[] = $chat;
                $seenIds[$chat['id']] = true;
            }
        }

        // 新規追加: 新興トレンドトピック分析
        $emergingTrendChats = $this->getEmergingTrendTopicsAnalysis($limit);
        foreach ($emergingTrendChats as $chat) {
            if (!isset($seenIds[$chat['id']])) {
                $chat['selection_source'] = 'emerging_trend_topic';
                $chat['analysis_reason'] = '新興トレンドを扱う将来性の高いトピック';
                $chat['emerging_trend_score'] = $chat['emerging_trend_score'] ?? 0;
                $candidates[] = $chat;
                $seenIds[$chat['id']] = true;
            }
        }

        // SQLite分析用のフィルター作成（既に収集済みの候補IDを使用）
        $candidateIds = array_keys($seenIds);
        
        if (!empty($candidateIds)) {
            // 10. 長期トレンド分析（SQLite統計データ活用）
            $longTermTrends = $this->getLongTermTrendAnalysis($candidateIds, $limit);
            foreach ($longTermTrends as $ltData) {
                if (!isset($seenIds[$ltData['id']])) {
                    $ltData['selection_source'] = 'long_term_trend';
                    $ltData['analysis_reason'] = '6ヶ月間の継続的な成長と高い一貫性を持つ長期安定成長株';
                    $candidates[] = $ltData;
                    $seenIds[$ltData['id']] = true;
                }
            }

            // 11. 季節性・周期性パターン分析
            $seasonalPatterns = $this->getSeasonalPatternAnalysis($candidateIds, $limit);
            foreach ($seasonalPatterns as $spData) {
                if (!isset($seenIds[$spData['id']])) {
                    $spData['selection_source'] = 'seasonal_pattern';
                    $spData['analysis_reason'] = '規則的な成長パターンと予測可能な季節性を持つ安定成長株';
                    $candidates[] = $spData;
                    $seenIds[$spData['id']] = true;
                }
            }

            // 12. 復活・回復パターン分析
            $recoveryPatterns = $this->getRecoveryPatternAnalysis($candidateIds, $limit);
            foreach ($recoveryPatterns as $rpData) {
                if (!isset($seenIds[$rpData['id']])) {
                    $rpData['selection_source'] = 'recovery_pattern';
                    $rpData['analysis_reason'] = '停滞期を経て再び成長に転じた復活・回復パターンの注目株';
                    $candidates[] = $rpData;
                    $seenIds[$rpData['id']] = true;
                }
            }
        }

        // フィルタリング：真にオープンなチャットのみを選出
        $candidates = array_filter($candidates, function($candidate) {
            return $this->isGenuinelyOpenChat($candidate);
        });

        // スコアリングして並び替え
        foreach ($candidates as &$candidate) {
            // 総合スコアを計算（各指標を正規化して加重平均）
            $candidate['ai_composite_score'] = $this->calculateCompositeScore($candidate);
        }

        // スコア順でソート
        usort($candidates, function ($a, $b) {
            return $b['ai_composite_score'] <=> $a['ai_composite_score'];
        });

        return $candidates;
    }

    /**
     * 長期トレンド分析（SQLite統計データ活用）
     * 数年分の日別人数データを分析して長期的な成長パターンを発見
     */
    public function getLongTermTrendAnalysis(array $chatIds, int $limit = 10): array
    {
        if (empty($chatIds)) {
            return [];
        }
        $chatIdPlaceholders = str_repeat('?,', count($chatIds) - 1) . '?';
        
        // 2. フィルタリング済みチャットのみでSQLite分析実行
        \App\Models\SQLite\SQLiteStatistics::connect();
        
        $query = "
            WITH recent_data AS (
                SELECT 
                    open_chat_id,
                    date,
                    member,
                    ROW_NUMBER() OVER (PARTITION BY open_chat_id ORDER BY date DESC) as rn
                FROM statistics 
                WHERE open_chat_id IN ($chatIdPlaceholders)
                    AND date >= date('now', '-45 days')
            ),
            weekly_growth AS (
                SELECT 
                    open_chat_id,
                    strftime('%Y-%W', date) as year_week,
                    MAX(member) - MIN(member) as weekly_change,
                    COUNT(*) as days_in_week
                FROM recent_data
                WHERE rn <= 45  -- 最近45日分のみ
                GROUP BY open_chat_id, strftime('%Y-%W', date)
                HAVING days_in_week >= 2
            ),
            trend_metrics AS (
                SELECT 
                    open_chat_id,
                    COUNT(*) as weeks_recorded,
                    AVG(weekly_change) as avg_weekly_growth,
                    SUM(CASE WHEN weekly_change > 0 THEN 1 ELSE 0 END) as growth_weeks,
                    MAX(weekly_change) as peak_weekly_growth,
                    -- 最新の勢い（直近2週間）
                    (SELECT AVG(wg.weekly_change) 
                     FROM weekly_growth wg 
                     WHERE wg.open_chat_id = weekly_growth.open_chat_id 
                       AND wg.year_week >= strftime('%Y-%W', date('now', '-14 days'))
                    ) as recent_momentum
                FROM weekly_growth
                GROUP BY open_chat_id
                HAVING weeks_recorded >= 3
            )
            SELECT 
                tm.open_chat_id,
                tm.weeks_recorded,
                ROUND(tm.avg_weekly_growth, 2) as avg_weekly_growth,
                tm.growth_weeks,
                tm.peak_weekly_growth,
                ROUND(COALESCE(tm.recent_momentum, 0), 2) as recent_momentum,
                (SELECT member FROM recent_data rd WHERE rd.open_chat_id = tm.open_chat_id AND rd.rn = 1) as current_members,
                -- 簡易長期スコア
                ROUND(
                    tm.avg_weekly_growth * 2.0 + 
                    COALESCE(tm.recent_momentum, 0) * 3.0 +
                    (tm.growth_weeks * 100.0 / tm.weeks_recorded), 2
                ) as long_term_potential_score
            FROM trend_metrics tm
            WHERE tm.avg_weekly_growth > 1
            ORDER BY long_term_potential_score DESC
            LIMIT :limit
        ";
        
        $stmt = \App\Models\SQLite\SQLiteStatistics::$pdo->prepare($query);
        foreach ($chatIds as $i => $chatId) {
            $stmt->bindValue($i + 1, $chatId, \PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $sqliteResults = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // SQLiteの結果をMySQLデータと統合
        $finalResults = [];
        foreach ($sqliteResults as $sqliteData) {
            $mysqlQuery = "
                SELECT 
                    oc.id,
                    oc.name,
                    oc.member,
                    oc.category,
                    LEFT(oc.description, 30) as description,
                    COALESCE(srw.diff_member, 0) as week_growth,
                    COALESCE(srd.diff_member, 0) as day_growth,
                    COALESCE(srh.diff_member, 0) as hour_growth
                FROM open_chat oc
                LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
                LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
                LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
                WHERE oc.id = :id
            ";

            $mysqlData = DB::fetchAll($mysqlQuery, ['id' => $sqliteData['open_chat_id']]);

            if (!empty($mysqlData)) {
                $merged = array_merge($mysqlData[0], $sqliteData);
                $merged['id'] = $merged['open_chat_id'];
                $finalResults[] = $merged;
            }
        }

        return $finalResults;
    }

    /**
     * 季節性・周期性パターン分析（SQLite統計データ活用）
     * 年間を通じた成長パターンの発見
     */
    public function getSeasonalPatternAnalysis(array $chatIds, int $limit = 8): array
    {
        if (empty($chatIds)) {
            return [];
        }
        $chatIdPlaceholders = str_repeat('?,', count($chatIds) - 1) . '?';
        
        // 2. SQLiteで季節性分析
        \App\Models\SQLite\SQLiteStatistics::connect();
        
        $query = "
            WITH recent_data AS (
                SELECT 
                    open_chat_id,
                    date,
                    member,
                    strftime('%w', date) as day_of_week
                FROM statistics 
                WHERE open_chat_id IN ($chatIdPlaceholders)
                    AND date >= date('now', '-30 days')
            ),
            weekly_patterns AS (
                SELECT 
                    open_chat_id,
                    strftime('%Y-%W', date) as year_week,
                    AVG(member) as avg_weekly_members,
                    MAX(member) - MIN(member) as weekly_growth,
                    COUNT(*) as days_recorded
                FROM recent_data
                GROUP BY open_chat_id, strftime('%Y-%W', date)
                HAVING days_recorded >= 3
            ),
            pattern_metrics AS (
                SELECT 
                    open_chat_id,
                    COUNT(*) as weeks_recorded,
                    AVG(weekly_growth) as avg_weekly_growth,
                    -- 成長の安定性
                    CASE 
                        WHEN COUNT(*) > 2
                        THEN (AVG(weekly_growth) / (
                            (MAX(weekly_growth) - MIN(weekly_growth)) / 2.0 + 1
                        ))
                        ELSE 0 
                    END as growth_stability,
                    -- 直近の勢い
                    (SELECT AVG(wp.weekly_growth) 
                     FROM weekly_patterns wp 
                     WHERE wp.open_chat_id = weekly_patterns.open_chat_id 
                       AND wp.year_week >= strftime('%Y-%W', date('now', '-14 days'))
                    ) as recent_momentum
                FROM weekly_patterns
                GROUP BY open_chat_id
                HAVING weeks_recorded >= 2
            )
            SELECT 
                pm.open_chat_id,
                pm.weeks_recorded,
                ROUND(pm.avg_weekly_growth, 2) as avg_weekly_growth,
                ROUND(pm.growth_stability, 3) as growth_stability,
                ROUND(COALESCE(pm.recent_momentum, 0), 2) as recent_momentum,
                (SELECT member FROM recent_data rd WHERE rd.open_chat_id = pm.open_chat_id ORDER BY date DESC LIMIT 1) as current_members,
                -- 季節性スコア
                ROUND(
                    pm.avg_weekly_growth * 2.0 + 
                    pm.growth_stability * 10.0 +
                    COALESCE(pm.recent_momentum, 0) * 1.5, 2
                ) as seasonal_pattern_score
            FROM pattern_metrics pm
            WHERE pm.avg_weekly_growth > 0.5
            ORDER BY seasonal_pattern_score DESC
            LIMIT :limit
        ";
        
        $stmt = \App\Models\SQLite\SQLiteStatistics::$pdo->prepare($query);
        foreach ($chatIds as $i => $chatId) {
            $stmt->bindValue($i + 1, $chatId, \PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $sqliteResults = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // SQLiteの結果をMySQLデータと統合
        $finalResults = [];
        foreach ($sqliteResults as $sqliteData) {
            $mysqlQuery = "
                SELECT 
                    oc.id,
                    oc.name,
                    oc.member,
                    oc.category,
                    LEFT(oc.description, 30) as description,
                    COALESCE(srw.diff_member, 0) as week_growth,
                    COALESCE(srd.diff_member, 0) as day_growth,
                    COALESCE(srh.diff_member, 0) as hour_growth
                FROM open_chat oc
                LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
                LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
                LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
                WHERE oc.id = :id
            ";

            $mysqlData = DB::fetchAll($mysqlQuery, ['id' => $sqliteData['open_chat_id']]);

            if (!empty($mysqlData)) {
                $merged = array_merge($mysqlData[0], $sqliteData);
                $merged['id'] = $merged['open_chat_id'];
                $finalResults[] = $merged;
            }
        }

        return $finalResults;
    }

    /**
     * 復活・回復パターン分析（SQLite統計データ活用）
     * 一時的に停滞したが再び成長に転じたチャットの発見
     */
    public function getRecoveryPatternAnalysis(array $chatIds, int $limit = 6): array
    {
        if (empty($chatIds)) {
            return [];
        }
        $chatIdPlaceholders = str_repeat('?,', count($chatIds) - 1) . '?';
        
        // 2. SQLiteで復活パターン分析
        \App\Models\SQLite\SQLiteStatistics::connect();
        
        $query = "
            WITH recent_data AS (
                SELECT 
                    open_chat_id,
                    date,
                    member,
                    LAG(member, 1) OVER (PARTITION BY open_chat_id ORDER BY date) as prev_member
                FROM statistics 
                WHERE open_chat_id IN ($chatIdPlaceholders)
                    AND date >= date('now', '-45 days')
                ORDER BY open_chat_id, date
            ),
            daily_changes AS (
                SELECT 
                    open_chat_id,
                    date,
                    member,
                    prev_member,
                    CASE 
                        WHEN prev_member IS NOT NULL 
                        THEN member - prev_member 
                        ELSE 0 
                    END as daily_change
                FROM recent_data
                WHERE prev_member IS NOT NULL
            ),
            recovery_metrics AS (
                SELECT 
                    open_chat_id,
                    COUNT(*) as total_days,
                    SUM(CASE WHEN daily_change > 0 THEN 1 ELSE 0 END) as growth_days,
                    SUM(CASE WHEN daily_change < 0 THEN 1 ELSE 0 END) as decline_days,
                    AVG(daily_change) as avg_daily_change,
                    -- 直近2週間の平均変化
                    (SELECT AVG(dc.daily_change) 
                     FROM daily_changes dc 
                     WHERE dc.open_chat_id = daily_changes.open_chat_id 
                       AND dc.date >= date('now', '-14 days')
                    ) as recent_momentum,
                    MAX(member) as peak_members,
                    MIN(member) as lowest_members
                FROM daily_changes
                GROUP BY open_chat_id
                HAVING total_days >= 15
            )
            SELECT 
                rm.open_chat_id,
                rm.total_days,
                rm.growth_days,
                rm.decline_days,
                ROUND(rm.avg_daily_change, 2) as avg_daily_change,
                ROUND(COALESCE(rm.recent_momentum, 0), 2) as recent_momentum,
                rm.peak_members,
                rm.lowest_members,
                (SELECT member FROM recent_data rd WHERE rd.open_chat_id = rm.open_chat_id ORDER BY date DESC LIMIT 1) as current_members,
                -- 復活スコア
                ROUND(
                    COALESCE(rm.recent_momentum, 0) * 5.0 +
                    (rm.growth_days * 100.0 / rm.total_days) * 2.0 +
                    (CASE WHEN rm.avg_daily_change > 0 THEN rm.avg_daily_change * 10 ELSE 0 END), 2
                ) as recovery_potential_score
            FROM recovery_metrics rm
            WHERE rm.recent_momentum > 0  -- 直近は成長している
                AND rm.avg_daily_change > -1  -- 大幅な衰退ではない
            ORDER BY recovery_potential_score DESC
            LIMIT :limit
        ";
        
        $stmt = \App\Models\SQLite\SQLiteStatistics::$pdo->prepare($query);
        foreach ($chatIds as $i => $chatId) {
            $stmt->bindValue($i + 1, $chatId, \PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $sqliteResults = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // SQLiteの結果をMySQLデータと統合
        $finalResults = [];
        foreach ($sqliteResults as $sqliteData) {
            $mysqlQuery = "
                SELECT 
                    oc.id,
                    oc.name,
                    oc.member,
                    oc.category,
                    LEFT(oc.description, 30) as description,
                    COALESCE(srw.diff_member, 0) as week_growth,
                    COALESCE(srd.diff_member, 0) as day_growth,
                    COALESCE(srh.diff_member, 0) as hour_growth
                FROM open_chat oc
                LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
                LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
                LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
                WHERE oc.id = :id
            ";

            $mysqlData = DB::fetchAll($mysqlQuery, ['id' => $sqliteData['open_chat_id']]);

            if (!empty($mysqlData)) {
                $merged = array_merge($mysqlData[0], $sqliteData);
                $merged['id'] = $merged['open_chat_id'];
                $finalResults[] = $merged;
            }
        }

        return $finalResults;
    }

    /**
     * 潜在的将来成長性分析（現在は成長していないが将来性の高いチャット発見）
     */
    public function getFutureGrowthPotentialAnalysis(int $limit = 10): array
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
                -- 潜在性指標1: 説明文の充実度とキーワード豊富さ
                ROUND(
                    LENGTH(oc.description) / 10.0 +
                    (LENGTH(oc.description) - LENGTH(REPLACE(oc.description, ' ', ''))) / 2.0 +
                    (CASE WHEN oc.description REGEXP '募集|歓迎|初心者|質問|相談|情報|共有|交流' THEN 15 ELSE 0 END)
                , 2) as content_richness_score,
                -- 潜在性指標2: カテゴリ内での適正規模（大きすぎず小さすぎない）
                ROUND(
                    CASE 
                        WHEN oc.member BETWEEN 50 AND 500 THEN 20
                        WHEN oc.member BETWEEN 20 AND 50 THEN 15  
                        WHEN oc.member BETWEEN 500 AND 2000 THEN 10
                        ELSE 5
                    END
                , 2) as optimal_size_score,
                -- 潜在性指標3: トピックの将来性（新技術、トレンド関連）
                ROUND(
                    (CASE WHEN oc.name REGEXP 'AI|人工知能|ChatGPT|副業|投資|NFT|仮想通貨|Web3|メタバース|VR' THEN 20 ELSE 0 END) +
                    (CASE WHEN oc.name REGEXP '起業|スタートアップ|フリーランス|在宅|リモート|オンライン' THEN 15 ELSE 0 END) +
                    (CASE WHEN oc.name REGEXP '学習|勉強|スキル|資格|転職|キャリア' THEN 12 ELSE 0 END) +
                    (CASE WHEN oc.name REGEXP '健康|ダイエット|筋トレ|美容|メンタル' THEN 10 ELSE 0 END)
                , 2) as trend_topic_score,
                -- 潜在性指標4: 安定性（急激な増減がない）
                ROUND(
                    CASE 
                        WHEN ABS(COALESCE(srh.diff_member, 0)) <= 2 
                             AND ABS(COALESCE(srd.diff_member, 0)) <= 5 
                             AND ABS(COALESCE(srw.diff_member, 0)) <= 20 
                        THEN 15
                        WHEN ABS(COALESCE(srh.diff_member, 0)) <= 5 
                             AND ABS(COALESCE(srd.diff_member, 0)) <= 10 
                        THEN 10
                        ELSE 5
                    END
                , 2) as stability_score,
                -- 総合将来性スコア
                ROUND(
                    (LENGTH(oc.description) / 10.0 +
                     (LENGTH(oc.description) - LENGTH(REPLACE(oc.description, ' ', ''))) / 2.0 +
                     (CASE WHEN oc.description REGEXP '募集|歓迎|初心者|質問|相談|情報|共有|交流' THEN 15 ELSE 0 END)) +
                    (CASE 
                        WHEN oc.member BETWEEN 50 AND 500 THEN 20
                        WHEN oc.member BETWEEN 20 AND 50 THEN 15  
                        WHEN oc.member BETWEEN 500 AND 2000 THEN 10
                        ELSE 5
                    END) +
                    ((CASE WHEN oc.name REGEXP 'AI|人工知能|ChatGPT|副業|投資|NFT|仮想通貨|Web3|メタバース|VR' THEN 20 ELSE 0 END) +
                     (CASE WHEN oc.name REGEXP '起業|スタートアップ|フリーランス|在宅|リモート|オンライン' THEN 15 ELSE 0 END) +
                     (CASE WHEN oc.name REGEXP '学習|勉強|スキル|資格|転職|キャリア' THEN 12 ELSE 0 END) +
                     (CASE WHEN oc.name REGEXP '健康|ダイエット|筋トレ|美容|メンタル' THEN 10 ELSE 0 END)) +
                    (CASE 
                        WHEN ABS(COALESCE(srh.diff_member, 0)) <= 2 
                             AND ABS(COALESCE(srd.diff_member, 0)) <= 5 
                             AND ABS(COALESCE(srw.diff_member, 0)) <= 20 
                        THEN 15
                        WHEN ABS(COALESCE(srh.diff_member, 0)) <= 5 
                             AND ABS(COALESCE(srd.diff_member, 0)) <= 10 
                        THEN 10
                        ELSE 5
                    END)
                , 2) as future_potential_score
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE 
                oc.member BETWEEN 10 AND 5000
                AND LENGTH(oc.description) >= 20
                AND oc.name IS NOT NULL
                AND oc.description IS NOT NULL
                -- 現在は大きな成長をしていない（将来性重視）
                AND COALESCE(srw.diff_member, 0) BETWEEN -10 AND 50
                AND COALESCE(srd.diff_member, 0) BETWEEN -5 AND 20
                AND COALESCE(srh.diff_member, 0) BETWEEN -2 AND 10
            ORDER BY future_potential_score DESC, content_richness_score DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * 新興トレンドトピック発見（話題性と将来性の複合分析）
     */
    public function getEmergingTrendTopicsAnalysis(int $limit = 8): array
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
                -- 新興トレンド度合い
                ROUND(
                    -- 最新技術・サービス関連
                    (CASE WHEN oc.name REGEXP '2024|2025|最新|新着|New|ニュー' THEN 10 ELSE 0 END) +
                    (CASE WHEN oc.name REGEXP 'ChatGPT|Claude|Gemini|AI|人工知能|生成AI|機械学習' THEN 25 ELSE 0 END) +
                    (CASE WHEN oc.name REGEXP 'TikTok|Instagram|YouTube|SNS|インフルエンサー|バズ' THEN 20 ELSE 0 END) +
                    (CASE WHEN oc.name REGEXP 'サブスク|サブスクリプション|定額|月額' THEN 15 ELSE 0 END) +
                    (CASE WHEN oc.name REGEXP 'ソロ活|おひとり様|一人|個人|パーソナル' THEN 18 ELSE 0 END) +
                    (CASE WHEN oc.name REGEXP 'SDGs|持続可能|エコ|環境|サステナブル' THEN 22 ELSE 0 END) +
                    (CASE WHEN oc.name REGEXP 'コスパ|タイパ|効率|時短|節約|お得' THEN 16 ELSE 0 END) +
                    -- ライフスタイル変化
                    (CASE WHEN oc.name REGEXP 'ワーケーション|二拠点|移住|田舎|地方' THEN 20 ELSE 0 END) +
                    (CASE WHEN oc.name REGEXP 'ミニマリスト|断捨離|整理|片付け|シンプル' THEN 14 ELSE 0 END)
                , 2) as emerging_trend_score,
                -- コミュニティ活性度
                ROUND(
                    (CASE WHEN oc.member > 100 THEN LOG10(oc.member) * 5 ELSE oc.member * 0.3 END) +
                    (CASE WHEN LENGTH(oc.description) > 100 THEN 10 ELSE LENGTH(oc.description) * 0.1 END)
                , 2) as community_vitality_score
            FROM open_chat oc
            LEFT JOIN statistics_ranking_hour srh ON oc.id = srh.open_chat_id
            LEFT JOIN statistics_ranking_day srd ON oc.id = srd.open_chat_id
            LEFT JOIN statistics_ranking_week srw ON oc.id = srw.open_chat_id
            WHERE 
                oc.member BETWEEN 20 AND 3000
                AND LENGTH(oc.description) >= 15
                AND (
                    oc.name REGEXP 'AI|ChatGPT|2024|2025|最新|TikTok|Instagram|サブスク|ソロ活|SDGs|コスパ|タイパ|ワーケーション|ミニマリスト' OR
                    oc.description REGEXP '新しい|最新|トレンド|流行|話題|注目|人気|今|現在'
                )
            HAVING emerging_trend_score > 10
            ORDER BY emerging_trend_score DESC, community_vitality_score DESC
            LIMIT :limit
        ";

        return DB::fetchAll($query, ['limit' => $limit]);
    }

    /**
     * 真にオープンなチャットかどうかを判定（ノイズ除去）
     */
    private function isGenuinelyOpenChat(array $chat): bool
    {
        $name = $chat['name'] ?? '';
        $description = $chat['description'] ?? '';
        
        // 1. 説明文が空または極端に短い場合は除外
        if (empty(trim($description)) || mb_strlen(trim($description)) < 10) {
            return false;
        }
        
        // 2. ファンルーム・公式アカウント系のパターンを除外
        $excludePatterns = [
            // ファンルーム系
            '/ファン/', '/ファンクラブ/', '/fc\s/', '/FC\s/', '/応援/', 
            '/推し/', '/オタク/', '/好きな人/', '/すきな人/',
            // 公式系
            '/公式/', '/official/', '/運営/', '/事務所/', 
            // 排他的・プライベート系
            '/招待/', '/限定/', '/メンバー限定/', '/プライベート/', '/private/',
            '/発言禁止/', '/観覧/', '/観戦/', '/見学/', '/閲覧のみ/',
            // 有名人・著名人系（名前のみのタイトル）
            '/^[あ-んア-ンa-zA-Z\s]+$/', // 単純な名前のみ
        ];
        
        foreach ($excludePatterns as $pattern) {
            if (preg_match($pattern, $name) || preg_match($pattern, $description)) {
                return false;
            }
        }
        
        // 3. 参加を促すキーワードがあるかチェック（積極的評価）
        $positivePatterns = [
            '/参加/', '/入会/', '/仲間/', '/一緒/', '/みんな/', '/みなさん/',
            '/初心者/', '/歓迎/', '/募集/', '/集まれ/', '/話そう/', '/語ろう/',
            '/交流/', '/共有/', '/情報/', '/雑談/', '/おしゃべり/', '/チャット/',
            '/質問/', '/相談/', '/アドバイス/'
        ];
        
        $hasPositiveSignal = false;
        foreach ($positivePatterns as $pattern) {
            if (preg_match($pattern, $name) || preg_match($pattern, $description)) {
                $hasPositiveSignal = true;
                break;
            }
        }
        
        // 4. メンバー数による判定（あまりに排他的でないか）
        $memberCount = $chat['member'] ?? 0;
        if ($memberCount > 10000 && !$hasPositiveSignal) {
            // 大規模だが参加を促すサインがない = 公式系の可能性
            return false;
        }
        
        if ($memberCount < 10 && !$hasPositiveSignal) {
            // 小規模すぎて排他的な可能性
            return false;
        }
        
        return true;
    }

    /**
     * 複合スコア計算（AI選出用・多様性改善版）
     */
    private function calculateCompositeScore(array $chat): float
    {
        // 1. 正規化された成長スコア（加法式でバランス改善）
        $hourGrowth = min(($chat['hour_growth'] ?? 0), 100); // 上限設定
        $dayGrowth = min(($chat['day_growth'] ?? 0), 500);
        $weekGrowth = min(($chat['week_growth'] ?? 0), 2000);
        
        // 成長スコア：短期・中期・長期のバランス調整
        $growthScore = ($hourGrowth * 2) + ($dayGrowth * 1.5) + ($weekGrowth * 1);
        
        // メンバー数に基づく正規化（極端な偏りを軽減）
        $memberCount = $chat['member'] ?? 0;
        if ($memberCount > 0) {
            $growthScore = $growthScore / log10($memberCount + 10); // 対数正規化
        }

        // 2. サイズ調整（極端な偏りを軽減、加法式）
        $sizeBonus = 0;
        if ($memberCount < 300) {
            $sizeBonus = 15; // 小規模ボーナス
        } elseif ($memberCount < 1000) {
            $sizeBonus = 10; // 中小規模ボーナス
        } elseif ($memberCount < 5000) {
            $sizeBonus = 5;  // 中規模ボーナス
        } elseif ($memberCount > 10000) {
            $sizeBonus = -5; // 大規模ペナルティ（軽微）
        }

        // 3. 分析ソース別スコア（乗算から加算に変更で極端な変動を抑制）
        $sourceBonus = match ($chat['selection_source'] ?? '') {
            'viral_pattern' => 20,
            'pre_viral' => 25,
            'real_time_acceleration' => 22,
            'anomaly' => 30,
            'trend_prediction' => 18,
            'low_competition_segment' => 16,
            'long_term_trend' => 24,
            'seasonal_pattern' => 18,
            'recovery_pattern' => 22,
            // 新規追加分析手法（バランス調整）
            'momentum_surge' => 26,
            'hidden_gem' => 24,
            'breakthrough_timing' => 28,
            'market_disruption' => 30,
            'community_magnetism' => 22,
            'exponential_curve' => 25,
            // 将来性分析手法
            'future_growth_potential' => 32,
            'emerging_trend_topic' => 28,
            default => 10
        };

        // 4. 特別スコア（正規化して加算）
        $specialScore = 0;
        $specialScores = [
            'viral_potential_score' => 0.3,
            'anomaly_score' => 0.4,
            'trend_prediction_score' => 0.25,
            'acceleration_score' => 0.35,
            'long_term_potential_score' => 0.4,
            'seasonal_pattern_score' => 0.3,
            'recovery_potential_score' => 0.35,
            'momentum_surge_score' => 0.4,
            'hidden_gem_score' => 0.35,
            'breakthrough_timing_score' => 0.45,
            // 新規追加の将来性スコア
            'future_potential_score' => 0.5,
            'emerging_trend_score' => 0.45
        ];
        
        foreach ($specialScores as $key => $weight) {
            if (isset($chat[$key])) {
                $specialScore += min((float)$chat[$key] * $weight, 50); // 上限設定
            }
        }

        // 5. 最終スコア計算（加法式で安定性向上）
        $finalScore = $growthScore + $sizeBonus + $sourceBonus + $specialScore;
        
        // 6. スコア範囲の正規化（0-100点範囲）
        $finalScore = max(0, min(100, $finalScore));
        
        return round($finalScore, 2);
    }
}
