<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater;

use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatUpdaterDtoFactory;
use App\Services\OpenChat\Updater\Finalizer\OpenChatUpdaterDtoFinalizer;
use App\Services\OpenChat\Updater\Process\OpenChatUpdaterProcess;
use App\Models\Repositories\LogRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class OpenChatUpdater implements OpenChatUpdaterInterface
{
    private OpenChatDataForUpdaterWithCacheRepositoryInterface $openChatRepository;
    private OpenChatUpdaterDtoFactory $openChatUpdaterDtoFactory;
    private OpenChatUpdaterDtoFinalizer $openChatUpdaterDtoFinalizer;
    private OpenChatUpdaterProcess $openChatUpdaterProcess;
    private LogRepositoryInterface $logRepository;

    function __construct(
        OpenChatDataForUpdaterWithCacheRepositoryInterface $openChatRepository,
        OpenChatUpdaterDtoFactory $openChatUpdaterDtoFactory,
        OpenChatUpdaterDtoFinalizer $openChatUpdaterDtoFinalizer,
        OpenChatUpdaterProcess $openChatUpdaterProcess,
        LogRepositoryInterface $logRepository,
    ) {
        $this->openChatRepository = $openChatRepository;
        $this->openChatUpdaterDtoFactory = $openChatUpdaterDtoFactory;
        $this->openChatUpdaterDtoFinalizer = $openChatUpdaterDtoFinalizer;
        $this->openChatUpdaterProcess = $openChatUpdaterProcess;
        $this->logRepository = $logRepository;
    }

    function updateOpenChat(int $open_chat_id, OpenChatDto $ocDto): void
    {
        $repoDto = $this->openChatRepository->getOpenChatDataById($open_chat_id);
        if ($repoDto === false) {
            $this->logRepository->logUpdateOpenChatError($open_chat_id, '更新対象のレコードが見つかりませんでした');
            return;
        }

        if (OpenChatServicesUtility::containsHashtagNolog($ocDto->desc)) {
            $updaterDto = $this->openChatUpdaterDtoFactory->mapToDeleteOpenChatDto($open_chat_id, $repoDto->profileImageObsHash);
        } else {
            $updaterDto = $this->openChatUpdaterDtoFinalizer->finalizeUpdaterDtoGeneration(
                $this->openChatUpdaterDtoFactory->mapToDto($open_chat_id, $repoDto, $ocDto)
            );
        }

        $this->openChatUpdaterProcess->processUpdateOpenChat($updaterDto);
    }
}
