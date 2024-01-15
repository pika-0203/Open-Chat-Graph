<?php

declare(strict_types=1);

namespace Tests\Services\OpenChat;

use App\Services\StaticDataGeneration;
use PHPUnit\Framework\TestCase;

class StaticDataGenerationTest extends TestCase
{
    public function test(): void
    {
        /**
         * @var StaticDataGeneration $ssg
         */
        $ssg = app(StaticDataGeneration::class);
        $ssg->generateLatestOpenChatStaticData();

        $this->assertTrue(is_object($ssg));
    }

    public function testgenerateLatestStatisticsStaticData(): void
    {
        /**
         * @var StaticDataGeneration $ssg
         */
        $ssg = app(StaticDataGeneration::class);
        $ssg->generateLatestStatisticsStaticData();

        $this->assertTrue(is_object($ssg));
    }
}
