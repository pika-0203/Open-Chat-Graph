<?php

declare(strict_types=1);

use App\Config\AppConfig;
use PHPUnit\Framework\TestCase;

class SyncOpenChatProvisionalHalfTest extends TestCase
{
    public function test()
    {
        $arg = escapeshellarg('/th');

        $path = AppConfig::ROOT_PATH . '/cron/cron_half_check_provisional.php';
        exec(PHP_BINARY . " {$path} {$arg}");
        
        $this->assertTrue(true);
    }
}