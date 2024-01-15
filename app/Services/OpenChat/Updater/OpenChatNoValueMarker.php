<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater;

use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatUpdaterDtoFactory;

class OpenChatNoValueMarker
{
    private UpdateOpenChatRepositoryInterface $updateRepository;
    private OpenChatUpdaterDtoFactory $openChatUpdaterDtoFactory;

    function __construct(
        UpdateOpenChatRepositoryInterface $updateRepository,
        OpenChatUpdaterDtoFactory $openChatUpdaterDtoFactory,
    ) {
        $this->updateRepository = $updateRepository;
        $this->openChatUpdaterDtoFactory = $openChatUpdaterDtoFactory;
    }

    function markAsNoAliveOpenChat(int $open_chat_id): void
    {
        $this->updateRepository->updateOpenChatRecord(
            $this->openChatUpdaterDtoFactory->mapToNoAliveOpenChatDto($open_chat_id)
        );
    }

    function markAsNoEmidOpenChat(int $open_chat_id): string|false
    {
        $this->updateRepository->updateOpenChatRecord(
            $this->openChatUpdaterDtoFactory->mapToNoEmidOpenChatDto($open_chat_id)
        );

        return $this->updateRepository->getOpenChatUrlById($open_chat_id);
    }
}
