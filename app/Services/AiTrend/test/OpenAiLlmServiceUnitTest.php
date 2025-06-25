<?php

declare(strict_types=1);

namespace App\Services\AiTrend\test;

use PHPUnit\Framework\TestCase;
use App\Services\AiTrend\OpenAiLlmService;

/**
 * OpenAiLlmService の簡素化テスト
 */
class OpenAiLlmServiceUnitTest extends TestCase
{
    public OpenAiLlmService $inst;
    public function testService(): void
    {
        $this->inst = app(OpenAiLlmService::class);
        $res = $this->inst->generateManagerAnalysis();
        var_dump($res);
        $this->assertTrue(!!$res);
    }
}
