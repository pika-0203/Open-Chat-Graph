<?php

declare(strict_types=1);

namespace App\Services\AiTrend\Repository\test;

use PHPUnit\Framework\TestCase;
use App\Services\AiTrend\Repository\AiTrendAnalysisRepository;
use App\Models\Repositories\DB;

/**
 * AiTrendAnalysisRepository の簡素化テスト
 */
class AiTrendAnalysisRepositoryTest extends TestCase
{
    private AiTrendAnalysisRepository $repository;

    protected function setUp(): void
    {
        $this->repository = app(AiTrendAnalysisRepository::class);
        DB::connect();
    }

    public function testGetHiddenViralPatterns(): void
    {
        $result = $this->repository->getHiddenViralPatterns(5);
        
        $this->assertIsArray($result);
        $this->assertLessThanOrEqual(5, count($result));
        
        if (!empty($result)) {
            $firstItem = $result[0];
            $this->assertArrayHasKey('id', $firstItem);
            $this->assertArrayHasKey('name', $firstItem);
            $this->assertArrayHasKey('current_members', $firstItem);
        }
    }

    public function testGetLowCompetitionHighGrowthSegments(): void
    {
        $result = $this->repository->getLowCompetitionHighGrowthSegments(5);
        
        $this->assertIsArray($result);
        $this->assertLessThanOrEqual(5, count($result));
        
        if (!empty($result)) {
            $firstItem = $result[0];
            $this->assertArrayHasKey('category', $firstItem);
            $this->assertArrayHasKey('total_chats_in_category', $firstItem);
        }
    }

    public function testGetCurrentGrowthAcceleration(): void
    {
        $result = $this->repository->getCurrentGrowthAcceleration(3);
        
        $this->assertIsArray($result);
        $this->assertLessThanOrEqual(3, count($result));
        
        if (!empty($result)) {
            $firstItem = $result[0];
            $this->assertArrayHasKey('id', $firstItem);
            $this->assertArrayHasKey('name', $firstItem);
        }
    }

    public function testGetPreViralIndicators(): void
    {
        $result = $this->repository->getPreViralIndicators(5);
        
        $this->assertIsArray($result);
        $this->assertLessThanOrEqual(5, count($result));
        
        if (!empty($result)) {
            $firstItem = $result[0];
            $this->assertArrayHasKey('id', $firstItem);
        }
    }

    public function testGetNewEntrantOpportunities(): void
    {
        $result = $this->repository->getNewEntrantOpportunities(5);
        
        $this->assertIsArray($result);
        $this->assertLessThanOrEqual(5, count($result));
        
        if (!empty($result)) {
            $firstItem = $result[0];
            $this->assertArrayHasKey('category', $firstItem);
        }
    }

    public function testBasicFunctionality(): void
    {
        // 基本的なリポジトリの機能確認
        $this->assertInstanceOf(AiTrendAnalysisRepository::class, $this->repository);
    }
}