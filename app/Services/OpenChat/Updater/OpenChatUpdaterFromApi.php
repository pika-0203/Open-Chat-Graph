<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater;

use App\Services\OpenChat\Crawler\OpenChatDtoFetcherInterface;
use App\Services\OpenChat\Crawler\OpenChatApiFromEmidDownloader;
use App\Services\OpenChat\Crawler\OpenChatUrlChecker;
use App\Models\Repositories\Log\LogRepositoryInterface;

class OpenChatUpdaterFromApi implements OpenChatUpdaterWithFetchInterface
{
    private OpenChatUpdaterInterface $openChatUpdater;
    private OpenChatDtoFetcherInterface $openChatDtoFetcher;
    private OpenChatUrlChecker $openChatUrlChecker;
    private OpenChatNoValueMarker $openChatNoValueMarker;
    private LogRepositoryInterface $logRepository;
    private OpenChatUpdaterFromPage $openChatUpdaterFromPage;

    function __construct(
        OpenChatUpdaterInterface $openChatUpdater,
        OpenChatApiFromEmidDownloader $openChatDtoFetcher,
        OpenChatUrlChecker $openChatUrlChecker,
        OpenChatNoValueMarker $openChatNoValueMarker,
        LogRepositoryInterface $logRepository,
        OpenChatUpdaterFromPage $openChatUpdaterFromPage,
    ) {
        $this->openChatUpdater = $openChatUpdater;
        $this->openChatDtoFetcher = $openChatDtoFetcher;
        $this->openChatUrlChecker = $openChatUrlChecker;
        $this->openChatNoValueMarker = $openChatNoValueMarker;
        $this->logRepository = $logRepository;
        $this->openChatUpdaterFromPage = $openChatUpdaterFromPage;
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
            return $this->updatePrivateOpenChat($open_chat_id);
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
            $this->openChatNoValueMarker->markAsNoAliveOpenChat($open_chat_id);
        }

        return true;
    }

    function updatePrivateOpenChat(int $id): bool
    {
        $fetcherArg = $this->openChatNoValueMarker->markAsNoEmidOpenChat($id);
        if (!$fetcherArg) {
            return false;
        }

        return $this->openChatUpdaterFromPage->fetchUpdateOpenChat(compact('id', 'fetcherArg'));
    }
}
