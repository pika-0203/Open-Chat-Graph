<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Models\Repositories\Log\LogRepositoryInterface;
use App\Services\Utility\ErrorCounter;
use App\Services\OpenChat\Updater\OpenChatUpdaterFromApi;
use App\Services\Crawler\CrawlerFactory;

class OpenChatDailyCrawling
{
    function __construct(

        private OpenChatUpdaterFromApi $openChatUpdater,
        private LogRepositoryInterface $logRepository,
        private ErrorCounter $errorCounter,
    ) {
    }

    /**
     * @param int[] $openChatIdArray
     * @throws \RuntimeException
     */
    function crawling(array $openChatIdArray, ?int $intervalSecond = null): int
    {
        foreach ($openChatIdArray as $id) {
            $result = $this->openChatUpdater->fetchUpdateOpenChat($id);

            if ($result === false) {
                $this->errorCounter->increaseCount();
            } else {
                $this->errorCounter->resetCount();
            }

            if ($this->errorCounter->hasExceededMaxErrors()) {
                $message = 'crawlingProcess: 連続エラー回数が上限を超えました ' . $this->logRepository->getRecentLog();
                throw new \RuntimeException($message);
            }

            if ($intervalSecond) {
                CrawlerFactory::sleepInIntervalWithElapsedTime($intervalSecond);
            }
        }

        return count($openChatIdArray);
    }
}
