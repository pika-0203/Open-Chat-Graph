<?php

use PHPUnit\Framework\TestCase;
use App\Services\OpenChat\RisingPositionCrawling;

class RisingPositionCrawlingTest extends TestCase
{
    public function test_risingPositionCrawling()
    {
        /**
         * @var RisingPositionCrawling $test
         */
        $test = app(RisingPositionCrawling::class);

        $test->risingPositionCrawling();

        $this->assertTrue(true);
    }
}
