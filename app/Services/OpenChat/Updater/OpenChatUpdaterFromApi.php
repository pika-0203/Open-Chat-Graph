<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater;

use App\Services\OpenChat\Crawler\OpenChatApiFromEmidDownloader;
use App\Services\OpenChat\Crawler\OpenChatUrlChecker;
use App\Models\Repositories\Log\LogRepositoryInterface;

class OpenChatUpdaterFromApi
{
    function __construct(
        private OpenChatUpdater $openChatUpdater,
        private OpenChatUrlChecker $openChatUrlChecker,
        private LogRepositoryInterface $logRepository,
        private OpenChatApiFromEmidDownloader $openChatDtoFetcher,
    ) {
    }

    /**
     * @param array $openChat `['id' => int, 'fetcherArg' => emid]`
     */
    function fetchUpdateOpenChat(array $openChat): bool
    {
        /**
         * @var int $open_chat_id 
         * @var string $fetcherArg
         */
        ['id' => $open_chat_id, 'fetcherArg' => $fetcherArg] = $openChat;

        try {
            $ocDto = $this->openChatDtoFetcher->fetchOpenChatDto($fetcherArg);
        } catch (\RuntimeException $e) {
            $this->logRepository->logUpdateOpenChatError($open_chat_id, $e->getMessage());

            return false;
        }

        if ($ocDto === false || $ocDto->memberCount < 1) {
            // 削除
            $this->openChatUpdater->updateOpenChat($open_chat_id, false);
            return true;
        }

        try {
            $isAlive = $this->openChatUrlChecker->isOpenChatUrlAvailable($ocDto->invitationTicket);
        } catch (\RuntimeException $e) {
            $this->logRepository->logUpdateOpenChatError($open_chat_id, $e->getMessage());

            return false;
        }

        if ($isAlive) {
            $this->openChatUpdater->updateOpenChat($open_chat_id, $ocDto);
        } else {
            // 削除
            $this->openChatUpdater->updateOpenChat($open_chat_id, false);
        }

        return true;
    }
}
