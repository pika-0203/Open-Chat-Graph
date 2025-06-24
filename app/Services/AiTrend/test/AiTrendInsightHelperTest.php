<?php

declare(strict_types=1);

namespace App\Services\AiTrend\test;

use PHPUnit\Framework\TestCase;

// ヘルパー関数を読み込み
require_once __DIR__ . '/../Helpers/ai_trend_insight_helper.php';

/**
 * AiTrendInsightHelper の単体テスト
 */
class AiTrendInsightHelperTest extends TestCase
{
    /**
     * @test
     */
    public function testGenerateInsightTextWithRevolutionaryPotential(): void
    {
        $chat = ['revolutionary_potential' => 'breakthrough'];
        
        $result = getAiInsightText($chat);
        
        $this->assertStringContainsString('業界を変革する可能性', $result);
        $this->assertStringContainsString('突破口的コミュニティ', $result);
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextWithDisruptivePotential(): void
    {
        $chat = ['revolutionary_potential' => 'disruptive'];
        
        $result = getAiInsightText($chat);
        
        $this->assertStringContainsString('破壊的革新', $result);
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextWithInnovativePotential(): void
    {
        $chat = ['revolutionary_potential' => 'innovative'];
        
        $result = getAiInsightText($chat);
        
        $this->assertStringContainsString('革新的トピック', $result);
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextWithEmergingPotential(): void
    {
        $chat = ['revolutionary_potential' => 'emerging'];
        
        $result = getAiInsightText($chat);
        
        $this->assertStringContainsString('急成長が期待', $result);
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextWithGrowthAcceleration(): void
    {
        $chat = ['growth_acceleration_pattern' => 'hyper_acceleration'];
        
        $result = getAiInsightText($chat);
        
        $this->assertStringContainsString('異常な成長加速度', $result);
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextWithStrongAcceleration(): void
    {
        $chat = ['growth_acceleration_pattern' => 'strong_acceleration'];
        
        $result = getAiInsightText($chat);
        
        $this->assertStringContainsString('強い成長加速傾向', $result);
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextWithSteadyGrowth(): void
    {
        $chat = ['growth_acceleration_pattern' => 'steady'];
        
        $result = getAiInsightText($chat);
        
        $this->assertStringContainsString('安定した成長パターン', $result);
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextWithLifecycleStage(): void
    {
        $testCases = [
            'viral_birth' => 'バイラル拡散の初期段階',
            'growth_phase' => '成長期にあり',
            'maturity_resilience' => '成熟期でも安定',
            'veteran_stability' => '長期運営でも継続'
        ];
        
        foreach ($testCases as $stage => $expectedText) {
            $chat = ['lifecycle_stage' => $stage];
            $result = getAiInsightText($chat);
            $this->assertStringContainsString($expectedText, $result);
        }
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextWithTopicTrend(): void
    {
        $testCases = [
            'AI_tech_trend' => 'AI・技術トレンドの最前線',
            'finance_trend' => '金融・投資分野',
            'hallyu_wave' => '韓流ブーム',
            'gaming_culture' => 'ゲーム文化',
            'work_revolution' => '働き方革命'
        ];
        
        foreach ($testCases as $trend => $expectedText) {
            $chat = ['topic_trend_classification' => $trend];
            $result = getAiInsightText($chat);
            $this->assertStringContainsString($expectedText, $result);
        }
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextWithHighUniqueness(): void
    {
        $chat = ['uniqueness_quotient' => 0.8];
        
        $result = getAiInsightText($chat);
        
        $this->assertStringContainsString('独自性の高い', $result);
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextWithGrowthAmountFallback(): void
    {
        $testCases = [
            ['growth_amount' => 1500, 'expected' => '急激な成長により'],
            ['growth_amount' => 700, 'expected' => '堅実な成長トレンド'],
            ['growth_amount' => 200, 'expected' => '着実な成長で'],
            ['growth_amount' => 50, 'expected' => 'AI分析により特別な価値']
        ];
        
        foreach ($testCases as $testCase) {
            $chat = ['growth_amount' => $testCase['growth_amount']];
            $result = getAiInsightText($chat);
            $this->assertStringContainsString($testCase['expected'], $result);
        }
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextWithWeekGrowthAmountFallback(): void
    {
        $chat = ['week_growth_amount' => 1200];
        
        $result = getAiInsightText($chat);
        
        $this->assertStringContainsString('急激な成長により', $result);
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextWithEmptyData(): void
    {
        $chat = [];
        
        $result = getAiInsightText($chat);
        
        $this->assertEquals('AI分析により特別な価値を持つと判定', $result);
    }
    
    /**
     * @test
     */
    public function testGetPotentialLabel(): void
    {
        $testCases = [
            'breakthrough' => '突破口',
            'disruptive' => '破壊的',
            'innovative' => '革新的',
            'emerging' => '新興',
            'high' => '高',
            'medium' => '中',
            'low' => '低',
            'unknown' => 'unknown'
        ];
        
        foreach ($testCases as $input => $expected) {
            $this->assertEquals($expected, getPotentialLabel($input));
        }
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextPriority(): void
    {
        // 複数の属性がある場合、最初に見つかったものを使用することを確認
        $chat = [
            'revolutionary_potential' => 'breakthrough',
            'growth_acceleration_pattern' => 'steady',
            'lifecycle_stage' => 'viral_birth'
        ];
        
        $result = getAiInsightText($chat);
        
        // breakthrough が最優先で選ばれることを確認
        $this->assertStringContainsString('業界を変革する可能性', $result);
        $this->assertStringNotContainsString('安定した成長', $result);
    }
    
    /**
     * @test
     */
    public function testGenerateInsightTextWithComplexData(): void
    {
        $chat = [
            'revolutionary_potential' => 'innovative',
            'growth_acceleration_pattern' => 'hyper_acceleration',
            'lifecycle_stage' => 'growth_phase',
            'topic_trend_classification' => 'AI_tech_trend',
            'uniqueness_quotient' => 0.9,
            'growth_amount' => 1000
        ];
        
        $result = getAiInsightText($chat);
        
        // 最初に該当する insight（revolutionary_potential）が使用されることを確認
        $this->assertStringContainsString('革新的トピック', $result);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }
}