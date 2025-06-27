<?php

declare(strict_types=1);

namespace App\Services\AiTrend\Helpers;

use App\Config\AppConfig;
use Shared\MimimalCmsConfig;

/**
 * AI分析結果表示用のヘルパー関数（簡素化版）
 */
class AiTrendInsightHelper
{
    /**
     * AI考察テキストを生成
     */
    public static function generateInsightText(array $chat): string
    {
        // フォールバック処理（基本的な成長データに基づく）
        $growthAmount = $chat['growth_amount'] ?? $chat['week_growth_amount'] ?? 0;
        if ($growthAmount > 1000) {
            return "急激な成長により大きな注目を集めている";
        } elseif ($growthAmount > 500) {
            return "堅実な成長トレンドで将来性が高い";
        } elseif ($growthAmount > 100) {
            return "着実な成長で安定した人気を獲得中";
        }

        return "AI分析により特別な価値を持つと判定";
    }

    /**
     * 成長可能性の日本語表示
     */
    public static function getPotentialLabel(string $potential): string
    {
        switch ($potential) {
            case 'breakthrough':
                return '躍進';
            case 'disruptive':
                return '画期的';
            case 'innovative':
                return '新しい';
            case 'emerging':
                return '注目';
            case 'high':
                return '高';
            case 'medium':
                return '中';
            case 'low':
                return '低';
            default:
                return $potential;
        }
    }

    /**
     * カテゴリIDをカテゴリ名に変換
     */
    public static function getCategoryName($categoryId): string
    {
        if ($categoryId === null || $categoryId === '') {
            return 'その他';
        }

        // 現在の言語/地域を取得
        $urlRoot = MimimalCmsConfig::$urlRoot ?? '';
        
        // カテゴリマップを取得
        $categoryMap = AppConfig::OPEN_CHAT_CATEGORY[$urlRoot] ?? AppConfig::OPEN_CHAT_CATEGORY[''];
        
        // カテゴリIDでカテゴリ名を検索
        foreach ($categoryMap as $categoryName => $id) {
            if ($id == $categoryId) {
                return $categoryName;
            }
        }
        
        return 'その他';
    }
}
