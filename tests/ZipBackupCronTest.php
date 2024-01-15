<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Controllers\Cron\ZipBackupCron;
use App\Services\CronJson\SQLiteBackupCronState;

class ZipBackupCronTest extends TestCase
{
    public function test()
    {
        /**
         * @var ZipBackupCron $cron
         */
        $cron = app(ZipBackupCron::class);

        $cron->ftpZipBackUp(app(SQLiteBackupCronState::class));

        $this->assertTrue(true);
    }
}
