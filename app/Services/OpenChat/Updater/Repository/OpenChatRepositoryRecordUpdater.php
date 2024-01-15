<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater\Repository;

use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatUpdaterDto;
use App\Services\OpenChat\Dto\ArchiveFlagsDto;

class OpenChatRepositoryRecordUpdater
{
    private UpdateOpenChatRepositoryInterface $updateRepository;

    function __construct(
        UpdateOpenChatRepositoryInterface $updateRepository,

    ) {
        $this->updateRepository = $updateRepository;
    }

    function updateOpenChatRepositoryRecord(OpenChatUpdaterDto $updaterDto, ArchiveFlagsDto|false $archiveFlagsDto): bool
    {
        if ($archiveFlagsDto) {
            $this->updateRepository->copyToOpenChatArchive($archiveFlagsDto);
        }

        return $this->updateRepository->updateOpenChatRecord($updaterDto);
    }
}
