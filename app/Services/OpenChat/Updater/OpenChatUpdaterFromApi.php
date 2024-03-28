<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater;

use App\Exceptions\InvalidMemberCountException;
use App\Services\OpenChat\Crawler\OpenChatApiFromEmidDownloader;
use App\Models\Repositories\Log\LogRepositoryInterface;
use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Services\OpenChat\Crawler\OpenChatUrlChecker;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\OpenChat\Dto\OpenChatRepositoryDto;
use App\Services\OpenChat\Updater\Process\OpenChatMargeUpdateProcess;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class OpenChatUpdaterFromApi
{
    private string $date;

    function __construct(
        private OpenChatDataForUpdaterWithCacheRepositoryInterface $openChatRepository,
        private StatisticsRepositoryInterface $statisticsRepository,
        private LogRepositoryInterface $logRepository,
        private OpenChatApiFromEmidDownloader $openChatDtoFetcher,
        private OpenChatUrlChecker $openChatUrlChecker,
        private OpenChatMargeUpdateProcess $openChatMargeUpdateProcess,
        private OpenChatImageStoreUpdater $openChatImageStoreUpdater,
    ) {
        $this->date = OpenChatServicesUtility::getCronModifiedStatsMemberDate();
    }

    /**
     * @param int $open_chat_id
     */
    function fetchUpdateOpenChat(int $open_chat_id): bool
    {
        $repoDto = $this->openChatRepository->getOpenChatDataById($open_chat_id);
        if ($repoDto === false) {
            $this->logRepository->logUpdateOpenChatError($open_chat_id, '更新対象のレコードが見つかりませんでした');
            return true;
        }

        try {
            $ocDto = $this->openChatDtoFetcher->fetchOpenChatDto($repoDto->emid);
            if ($ocDto) {
                $ocDto =
                    $this->openChatUrlChecker->isOpenChatUrlAvailable($ocDto->invitationTicket)
                    ? $ocDto
                    : false;
            }
        } catch (InvalidMemberCountException $e) {
            $this->logRepository->logUpdateOpenChatError($open_chat_id, $e->getMessage());
            $this->updateDelete($repoDto, false);
            return false;
        } catch (\RuntimeException $e) {
            $this->logRepository->logUpdateOpenChatError($open_chat_id, $e->getMessage());
            return false;
        }

        $this->updateDelete($repoDto, $ocDto);
        return true;
    }

    private function updateDelete(OpenChatRepositoryDto $repoDto, OpenChatDto|false $ocDto)
    {
        if (!$ocDto) {
            $this->openChatMargeUpdateProcess->mergeUpdateOpenChat($repoDto, $ocDto);
            return;
        }

        $updaterDto = true;
        if (
            $repoDto->name !== $ocDto->name
            || $repoDto->desc !== $ocDto->desc
            || $repoDto->profileImageObsHash !== $ocDto->profileImageObsHash
            || $repoDto->invitationTicket !== $ocDto->invitationTicket
            || $repoDto->memberCount !== $ocDto->memberCount
        ) {
            $updaterDto = !!$this->openChatMargeUpdateProcess->mergeUpdateOpenChat($repoDto, $ocDto);
        }

        $updaterDto && $this->statisticsRepository->insertDailyStatistics($repoDto->open_chat_id, $ocDto->memberCount, $this->date);
    }
}
