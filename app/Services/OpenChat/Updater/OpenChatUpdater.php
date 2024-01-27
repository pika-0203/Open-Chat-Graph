<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater;

use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatUpdaterDtoFactory;
use App\Services\OpenChat\Updater\Finalizer\OpenChatUpdaterDtoFinalizer;
use App\Models\Repositories\Log\LogRepositoryInterface;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class OpenChatUpdater
{
    function __construct(
        private OpenChatDataForUpdaterWithCacheRepositoryInterface $openChatRepository,
        private OpenChatUpdaterDtoFactory $openChatUpdaterDtoFactory,
        private OpenChatUpdaterDtoFinalizer $openChatUpdaterDtoFinalizer,
        private UpdateOpenChatRepositoryInterface $updateRepository,
        private LogRepositoryInterface $logRepository,
    ) {
    }

    function updateOpenChat(int $open_chat_id, OpenChatDto|false $ocDto): void
    {
        $repoDto = $this->openChatRepository->getOpenChatDataById($open_chat_id);
        if ($repoDto === false) {
            $this->logRepository->logUpdateOpenChatError($open_chat_id, '更新対象のレコードが見つかりませんでした');
            return;
        }

        if (!$ocDto || OpenChatServicesUtility::containsHashtagNolog($ocDto->desc)) {
            $updaterDto = $this->openChatUpdaterDtoFactory->mapToDeleteOpenChatDto($open_chat_id, $repoDto->profileImageObsHash);
        } else {
            $updaterDto = $this->openChatUpdaterDtoFinalizer->finalizeUpdaterDtoGeneration(
                $this->openChatUpdaterDtoFactory->mapToDto($open_chat_id, $repoDto, $ocDto)
            );
        }

        $this->updateRepository->updateOpenChatRecord($updaterDto);
    }
}
