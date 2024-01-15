<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Shadow\DB;
use App\Models\Repositories\LogRepository;

class LogRepositoryTest extends TestCase
{
    public LogRepository $log;

    public function test()
    {
        $this->log = app(LogRepository::class);
        
        $result = $this->log->getRecentLog();
        debug($result);

        $this->assertIsString($result);
    }
}
