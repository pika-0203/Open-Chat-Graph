<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater;

use App\Services\OpenChat\Crawler\OpenChatApiFromEmidDownloader;
use App\Models\Repositories\Log\LogRepositoryInterface;
use App\Services\OpenChat\Crawler\OpenChatUrlChecker;

class OpenChatUpdaterFromApi
{
    function __construct(
        private OpenChatUpdater $openChatUpdater,
        private LogRepositoryInterface $logRepository,
        private OpenChatApiFromEmidDownloader $openChatDtoFetcher,
        private OpenChatUrlChecker $openChatUrlChecker,
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

        if (
            $ocDto === false
            || $ocDto->memberCount < 1
            || !$this->openChatUrlChecker->isOpenChatUrlAvailable($ocDto->getApiDataInvitationTicket())
        ) {
            // 削除
            $this->openChatUpdater->updateOpenChat($open_chat_id, false);
            return true;
        }

        $this->openChatUpdater->updateOpenChat($open_chat_id, $ocDto);

        return true;
    }
}
