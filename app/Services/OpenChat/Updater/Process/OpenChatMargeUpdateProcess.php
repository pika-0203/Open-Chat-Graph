<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater\Process;

use App\Services\OpenChat\Dto\OpenChatUpdaterDtoFactory;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\OpenChat\Dto\OpenChatRepositoryDto;
use App\Services\OpenChat\Store\OpenChatImageStore;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class OpenChatMargeUpdateProcess
{
    function __construct(
        private OpenChatUpdaterDtoFactory $openChatUpdaterDtoFactory,
        private UpdateOpenChatRepositoryInterface $updateRepository,
        private OpenChatImageStore $openChatImageStore,
    ) {
    }

    function mergeUpdateOpenChat(OpenChatRepositoryDto $repoDto, OpenChatDto|false $ocDto, bool $updateMember = true): bool
    {
        if ($ocDto === false || OpenChatServicesUtility::containsHashtagNolog($ocDto)) {
            $updaterDto = $this->openChatUpdaterDtoFactory->mapToDeleteOpenChatDto($repoDto->open_chat_id);
            $this->updateRepository->updateOpenChatRecord($updaterDto);
            $this->openChatImageStore->deleteImage($repoDto->open_chat_id, $repoDto->profileImageObsHash);
            return false;
        }

        $updaterDto = $this->openChatUpdaterDtoFactory->mapToDto($repoDto, $ocDto, $updateMember);
        if ($updaterDto->profileImageObsHash) {
            //$this->openChatImageStore->downloadAndStoreOpenChatImage($updaterDto->open_chat_id, $updaterDto->profileImageObsHash);
            $this->updateRepository->updateOpenChatRecord($updaterDto);
            $this->openChatImageStore->deleteImage($repoDto->open_chat_id, $repoDto->profileImageObsHash);
        } else {
            $this->updateRepository->updateOpenChatRecord($updaterDto);
        }

        return true;
    }
}
