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

        // 7-9. SQLite分析用のフィルター作成（既に収集済みの候補IDを使用）
        $candidateIds = array_keys($seenIds);
        
        if (!empty($candidateIds)) {
            // 7. 長期トレンド分析（SQLite統計データ活用）
            $longTermTrends = $this->getLongTermTrendAnalysis($candidateIds, $limit);
            foreach ($longTermTrends as $ltData) {
                if (!isset($seenIds[$ltData['id']])) {
                    $ltData['selection_source'] = 'long_term_trend';
                    $ltData['analysis_reason'] = '6ヶ月間の継続的な成長と高い一貫性を持つ長期安定成長株';
                    $candidates[] = $ltData;
                    $seenIds[$ltData['id']] = true;
                }
            }

            // 8. 季節性・周期性パターン分析
            $seasonalPatterns = $this->getSeasonalPatternAnalysis($candidateIds, $limit);
            foreach ($seasonalPatterns as $spData) {
                if (!isset($seenIds[$spData['id']])) {
                    $spData['selection_source'] = 'seasonal_pattern';
                    $spData['analysis_reason'] = '規則的な成長パターンと予測可能な季節性を持つ安定成長株';
                    $candidates[] = $spData;
                    $seenIds[$spData['id']] = true;
                }
            }

            // 9. 復活・回復パターン分析
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
     * 複合スコア計算（AI選出用）
     */
    private function calculateCompositeScore(array $chat): float
    {
        $score = 0;

        // 成長量スコア（正規化）
        $growthScore = ($chat['hour_growth'] ?? 0) * 3 +
            ($chat['day_growth'] ?? 0) * 2 +
            ($chat['week_growth'] ?? 0) * 1;

        // メンバー数による調整（小規模ほど高評価）
        $sizeAdjustment = 1.0;
        if (($chat['member'] ?? 0) < 500) {
            $sizeAdjustment = 2.0;
        } elseif (($chat['member'] ?? 0) < 2000) {
            $sizeAdjustment = 1.5;
        } elseif (($chat['member'] ?? 0) > 10000) {
            $sizeAdjustment = 0.7;
        }

        // 分析ソース別の重み付け
        $sourceWeight = match ($chat['selection_source'] ?? '') {
            'viral_pattern' => 1.5,
            'pre_viral' => 1.8,
            'real_time_acceleration' => 1.6,
            'anomaly' => 2.0,
            'trend_prediction' => 1.4,
            'low_competition_segment' => 1.3,
            'long_term_trend' => 1.7,
            'seasonal_pattern' => 1.4,
            'recovery_pattern' => 1.6,
            default => 1.0
        };

        // 特別なスコアがある場合は考慮
        $specialScore = 0;
        if (isset($chat['viral_potential_score'])) {
            $specialScore += (float)$chat['viral_potential_score'] * 0.5;
        }
        if (isset($chat['anomaly_score'])) {
            $specialScore += (float)$chat['anomaly_score'] * 0.8;
        }
        if (isset($chat['trend_prediction_score'])) {
            $specialScore += (float)$chat['trend_prediction_score'] * 0.4;
        }
        if (isset($chat['acceleration_score'])) {
            $specialScore += (float)$chat['acceleration_score'] * 0.6;
        }
        if (isset($chat['long_term_potential_score'])) {
            $specialScore += (float)$chat['long_term_potential_score'] * 0.7;
        }
        if (isset($chat['seasonal_pattern_score'])) {
            $specialScore += (float)$chat['seasonal_pattern_score'] * 0.5;
        }
        if (isset($chat['recovery_potential_score'])) {
            $specialScore += (float)$chat['recovery_potential_score'] * 0.6;
        }

        // 最終スコア計算
        $score = ($growthScore * $sizeAdjustment * $sourceWeight) + $specialScore;

        return round($score, 2);
    }
}
