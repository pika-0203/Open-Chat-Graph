<?php

namespace App\Services\OpenChat\Utility;

use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatDto;

class OpenChatRejectUtility
{
    private array $rejectedEmid;

    function __construct(
        private OpenChatRepositoryInterface $openChatRepository
    ) {
    }

    /**
     * @return bool 登録を拒否している場合は true
     */
    function isRejectedOpenChat(OpenChatDto $dto): bool
    {
        if (!isset($rejectedEmid)) {
            $this->rejectedEmid = $this->openChatRepository->getRejectedEmidAll();
        }

        return in_array($dto->emid, $this->rejectedEmid, true);
    }
}
