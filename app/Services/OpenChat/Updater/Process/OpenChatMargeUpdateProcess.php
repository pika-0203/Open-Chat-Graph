<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater\Process;

use App\Services\OpenChat\Dto\OpenChatUpdaterDtoFactory;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\OpenChat\Dto\OpenChatRepositoryDto;
use App\Services\OpenChat\Dto\OpenChatUpdaterDto;
use App\Services\OpenChat\Updater\OpenChatDeleter;
use App\Services\OpenChat\Utility\OpenChatRejectUtility;

class OpenChatMargeUpdateProcess
{
    function __construct(
        private OpenChatUpdaterDtoFactory $openChatUpdaterDtoFactory,
        private UpdateOpenChatRepositoryInterface $updateRepository,
        private OpenChatDeleter $openChatDeleter,
        private OpenChatRejectUtility $openChatRejectUtility,
    ) {
    }

    function mergeUpdateOpenChat(OpenChatRepositoryDto $repoDto, OpenChatDto|false $ocDto, bool $updateMember = true): OpenChatUpdaterDto|false
    {
        if ($ocDto === false || $this->openChatRejectUtility->isRejectedOpenChat($ocDto)) {
            $this->openChatDeleter->deleteOpenChat($repoDto->open_chat_id, $repoDto->getLocalImgUrl());
            return false;
        }

        $updaterDto = $this->openChatUpdaterDtoFactory->mapToDto($repoDto, $ocDto, $updateMember);
        $this->updateRepository->updateOpenChatRecord($updaterDto);

        return $updaterDto;
    }
}
