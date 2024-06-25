<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Config\AppConfig;
use App\Models\Accreditation\AccreditationUserModel;
use App\Services\Cron\CronJson\AccreditationDataZipBackupCronState;

class AccreditationDataZipBackupCron
{
    function __construct(
        private ZipBackupCron $zipBackupCron,
        private AccreditationUserModel $accreditationUserModel
    ) {
    }

    function saveBackup(): string
    {
        $examFile = AppConfig::ACCREDITATION_DATA_FILE_PATH . '/exam/exam.dat';
        $userFile = AppConfig::ACCREDITATION_DATA_FILE_PATH . '/user/user.dat';

        saveSerializedFile(
            $examFile,
            $this->accreditationUserModel->getExamTableAll(),
            true
        );

        saveSerializedFile(
            $userFile,
            $this->accreditationUserModel->getUserTableAll(),
            true
        );

        return $this->zipBackupCron->ftpZipBackUp(app(AccreditationDataZipBackupCronState::class));
    }
}
