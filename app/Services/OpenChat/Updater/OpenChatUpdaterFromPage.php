<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater;

use App\Services\OpenChat\Crawler\OpenChatDtoFetcherInterface;
use App\Services\OpenChat\Crawler\OpenChatCrawler;
use App\Models\Repositories\Log\LogRepositoryInterface;

class OpenChatUpdaterFromPage implements OpenChatUpdaterWithFetchInterface
{
    private OpenChatUpdaterInterface $openChatUpdater;
    private OpenChatDtoFetcherInterface $openChatDtoFetcher;
    private OpenChatNoValueMarker $openChatNoValueMarker;
    private LogRepositoryInterface $logRepository;
    
    function __construct(
        OpenChatUpdaterInterface $openChatUpdater,
        OpenChatCrawler $openChatDtoFetcher,
        OpenChatNoValueMarker $openChatNoValueMarker,
        LogRepositoryInterface $logRepository,
    ) {
        $this->openChatUpdater = $openChatUpdater;
        $this->openChatDtoFetcher = $openChatDtoFetcher;
        $this->openChatNoValueMarker = $openChatNoValueMarker;
        $this->logRepository = $logRepository;
    }

    /**
     * @param array $openChat `['id' => int, 'fetcherArg' => url]`
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

        if ($ocDto === false) {
            $this->openChatNoValueMarker->markAsNoAliveOpenChat($open_chat_id);

            return false;
        }

        $this->openChatUpdater->updateOpenChat($open_chat_id, $ocDto);

        return true;
    }
}
