<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater\Process;

use App\Services\OpenChat\Updater\OpenChatUpdaterWithFetchInterface;
use App\Models\Repositories\LogRepositoryInterface;
use App\Services\Utility\ErrorCounter;
use App\Services\Crawler\CrawlerFactory;

class OpenChatCrawlingProcess
{
    private LogRepositoryInterface $logRepository;
    private ErrorCounter $errorCounter;

    function __construct(
        LogRepositoryInterface $logRepository,
        ErrorCounter $errorCounter
    ) {
        $this->logRepository = $logRepository;
        $this->errorCounter = $errorCounter;
    }

    function crawlingProcess(array $target, OpenChatUpdaterWithFetchInterface $openChatUpdaterWithFetch, ?int $intervalSecond = null): bool
    {
        foreach ($target as $openChat) {
            $result = $openChatUpdaterWithFetch->fetchUpdateOpenChat($openChat);

            if ($result === false) {
                $this->errorCounter->increaseCount();
            } else {
                $this->errorCounter->resetCount();
            }

            if ($this->errorCounter->hasExceededMaxErrors()) {
                $this->logRepository->logUpdateOpenChatError(0, 'crawlingProcess: 連続エラー回数が上限を超えました.');
                
                return false;
            }

            if ($intervalSecond) {
                CrawlerFactory::sleepInIntervalWithElapsedTime($intervalSecond);
            }
        }

        return true;
    }
}
