<?php

declare(strict_types=1);

use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use PHPUnit\Framework\TestCase;
use App\Services\SitemapGenerator;

class SitemapTest extends TestCase
{
    private SitemapGenerator $site;

    public function test()
    {
        debug(OpenChatServicesUtility::getModifiedCronTime('now'));
        debug(OpenChatServicesUtility::getModifiedCronTime(strtotime('hour')));

        $this->assertTrue(true);
    }
}
