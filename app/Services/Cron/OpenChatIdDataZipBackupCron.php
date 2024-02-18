<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Config\AppConfig;
use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Services\Cron\CronJson\OpenChatIdDataBackupCronState;

class OpenChatIdDataZipBackupCron
{
    function __construct(
        private ZipBackupCron $zipBackupCron,
        private OpenChatRepositoryInterface $openChatRepository
    ) {
    }

    function saveBackupIdEmidArray(): string
    {
        saveSerializedFile(
            AppConfig::OPEN_CHAT_ID_DATA_FILE_PATH . '/id/data.dat',
            $this->openChatRepository->getOpenChatIdEmidArrayAll(),
            true
        );

        return $this->zipBackupCron->ftpZipBackUp(app(OpenChatIdDataBackupCronState::class));
    }
}
