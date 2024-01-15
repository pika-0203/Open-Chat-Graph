<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Services\CrawlerUserAgents;

class CrawlerUserAgentsTest extends TestCase
{
    private CrawlerUserAgents $instance;

    public function test()
    {
        $this->instance = app(CrawlerUserAgents::class);

        debug($result = $this->instance->isCrawler("facebookexternalhit/1.1;line-poker/1.0"));

        $this->assertTrue($result);
        CrawlerUserAgents::fetchCrawlerUserAgentsJson();
    }
}
