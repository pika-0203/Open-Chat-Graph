<?php

declare(strict_types=1);

use App\Services\Cron\AccreditationDataZipBackupCron;
use PHPUnit\Framework\TestCase;

class AccreditationDataZipBackupCronTest extends TestCase
{
    public function test()
    {
        $inst = app(AccreditationDataZipBackupCron::class);
        debug($inst->saveBackup());
        $this->assertTrue(true);
    }
}
