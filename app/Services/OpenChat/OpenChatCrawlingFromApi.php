<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Updater\Process\OpenChatCrawlingProcess;
use App\Services\OpenChat\Updater\OpenChatUpdaterWithFetchInterface;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use App\Config\AppConfig;
use App\Services\OpenChat\Updater\OpenChatUpdaterFromApi;

class OpenChatCrawlingFromApi
{
    private OpenChatUpdaterWithFetchInterface $openChatUpdater;

    function __construct(
        private UpdateOpenChatRepositoryInterface $updateRepository,
        private OpenChatCrawlingProcess $openChatCrawlingProcess,
        OpenChatUpdaterFromApi $openChatUpdater,
    ) {
        $this->openChatUpdater = $openChatUpdater;
    }

    /**
     * @return array `[$count, $maxExecuteNum]`
     */
    function caluclatemaxExecuteNum(?int $limit = AppConfig::CRON_EXECUTE_COUNT): array
    {
        $target = $this->updateRepository->getUpdateFromApiTargetOpenChatId();
        $count = count($target);
        if ($limit) {
            $maxExecuteNum = OpenChatServicesUtility::caluclateMaxBatchNum(count($target), $limit);
        } else {
            $maxExecuteNum = 1;
        }

        return [$count, $maxExecuteNum];
    }

    function openChatCrawling(?int $limit = AppConfig::CRON_EXECUTE_COUNT): bool
    {
        $target = $this->updateRepository->getUpdateFromApiTargetOpenChatId($limit);
        if (empty($target)) {
            return true;
        }

        return $this->openChatCrawlingProcess->crawlingProcess($target, $this->openChatUpdater);
    }
}
