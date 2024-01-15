<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater\Process;

use App\Services\OpenChat\Updater\Repository\OpenChatRepositoryRecordUpdater;
use App\Services\OpenChat\Dto\OpenChatUpdaterDto;
use App\Services\OpenChat\Dto\ArchiveFlagsDtoFactory;

class OpenChatUpdaterProcess
{
    private OpenChatRepositoryRecordUpdater $openChatRecordUpdater;
    private ArchiveFlagsDtoFactory $archiveFlagsDtoFactory;
    private OpenChatUpdaterImageProcess $openChatUpdaterImageProcess;

    function __construct(
        OpenChatRepositoryRecordUpdater $openChatRecordUpdater,
        ArchiveFlagsDtoFactory $archiveFlagsDtoFactory,
        OpenChatUpdaterImageProcess $openChatUpdaterImageProcess,
    ) {
        $this->openChatRecordUpdater = $openChatRecordUpdater;
        $this->archiveFlagsDtoFactory = $archiveFlagsDtoFactory;
        $this->openChatUpdaterImageProcess = $openChatUpdaterImageProcess;
    }

    function processUpdateOpenChat(OpenChatUpdaterDto $updaterDto): void
    {
        $archiveFlagsDto = $this->archiveFlagsDtoFactory->generateArchiveFlagsDto($updaterDto);

        $this->openChatUpdaterImageProcess->processImageBeforeUpdate($updaterDto);
        $this->openChatRecordUpdater->updateOpenChatRepositoryRecord($updaterDto, $archiveFlagsDto);
        $this->openChatUpdaterImageProcess->processImageAfterUpdate($updaterDto, $archiveFlagsDto);
    }
}
