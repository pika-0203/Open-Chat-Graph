<?php

use PHPUnit\Framework\TestCase;
use App\Services\RankingPosition\Crawler\RisingPositionCrawling;
use App\Services\RankingPosition\Store\RisingPositionStore;

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

    public function testShowData()
    {
        /**
         * @var RisingPositionStore $test
         */
        $test = app(RisingPositionStore::class);

        [$fileTime, $data] = $test->getStorageData('2');

        debug($fileTime, array_slice($data, 0, 10));

        $this->assertTrue(true);
    }
}
