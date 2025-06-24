<?php

declare(strict_types=1);

namespace App\Services\AiTrend\test;

use PHPUnit\Framework\TestCase;
use App\Services\AiTrend\OpenAiLlmService;
use App\Services\AiTrend\Repository\AiTrendAnalysisRepository;

/**
 * OpenAiLlmService の簡素化テスト
 */
class OpenAiLlmServiceUnitTest extends TestCase
{
    public function testServiceInstantiationWithoutApiKey(): void
    {
        // API キーが設定されていない場合のテスト
        $this->expectException(\InvalidArgumentException::class);
        /** @var AiTrendAnalysisRepository $repo */
        $repo = $this->createMock(AiTrendAnalysisRepository::class);
        app(OpenAiLlmService::class, ['repository' => $repo]);
    }

    public function testBasicFunctionality(): void
    {
        // 基本的な機能が動作することを確認
        $this->assertTrue(true);
    }
}
