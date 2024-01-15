<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Services\OpenChat\Crawler\OpenChatApiRankingDownloader;
use App\Services\OpenChat\Crawler\OpenChatApiRankingDownloaderProcess;
use App\Services\OpenChat\Dto\OpenChatApiDtoFactory;
use App\Services\OpenChat\Updater\Process\OpenChatApiDbMergerProcess;
use App\Models\Repositories\LogRepositoryInterface;
use App\Services\RankingPosition\OpenChatRankingPositionStore;
use App\Config\AppConfig;
use App\Exceptions\ApplicationException;

class OpenChatApiDbMerger
{
    private OpenChatApiRankingDownloader $openChatApiRankingDataDownloader;
    private OpenChatApiDtoFactory $openChatApiDtoFactory;
    private OpenChatApiDbMergerProcess $openChatApiDbMergerProcess;
    private LogRepositoryInterface $logRepository;
    private OpenChatRankingPositionStore $openChatRankingPositionStore;

    function __construct(
        OpenChatApiRankingDownloaderProcess $openChatApiRankingDownloaderProcess,
        OpenChatApiDtoFactory $openChatApiDtoFactory,
        OpenChatApiDbMergerProcess $openChatApiDbMergerProcess,
        LogRepositoryInterface $logRepository,
        OpenChatRankingPositionStore $openChatRankingPositionStore,
    ) {
        $this->openChatApiRankingDataDownloader = app(OpenChatApiRankingDownloader::class, compact('openChatApiRankingDownloaderProcess'));
        $this->openChatApiDtoFactory = $openChatApiDtoFactory;
        $this->openChatApiDbMergerProcess = $openChatApiDbMergerProcess;
        $this->logRepository = $logRepository;
        $this->openChatRankingPositionStore = $openChatRankingPositionStore;
        
        deleteStorageFileAll(AppConfig::OPEN_CHAT_RANKING_POSITION_DIR, true);
    }

    function countMaxExecuteNum(int $limit): int
    {
        return $this->openChatApiRankingDataDownloader->countMaxExecuteNum($limit);
    }

    function fetchOpenChatApiRankingAll(int $limit, int $ExecuteNum, bool $updateFlag = true): int|false
    {
        try {
            $count = $this->fetchOpenChatApiRankingAllProcess($limit, $ExecuteNum, $updateFlag);
        } catch (\RuntimeException $e) {
            $this->logRepository->logUpdateOpenChatError(0, $e->__toString());

            return false;
        }

        $this->openChatRankingPositionStore->saveClearApiDataCache(AppConfig::OPEN_CHAT_RANKING_POSITION_DIR);
        return $count;
    }

    /**
     * API URL一件ずつの処理
     */
    private function fetchOpenChatApiRankingAllProcess(int $limit, int $ExecuteNum, bool $updateFlag): int
    {
        return $this->openChatApiRankingDataDownloader->fetchOpenChatApiRankingAll($limit, $ExecuteNum, function (array $apiData, string $category) use ($updateFlag) {
            $this->checkKillFlag();

            $callback = fn ($apiDto): ?string => $this->openChatApiDbMergerProcess->validateAndMapToOpenChatDtoCallback($apiDto, $updateFlag);
            $errors = $this->openChatApiDtoFactory->validateAndMapToOpenChatDto($apiData, $callback);

            foreach ($errors as $error) {
                $this->logRepository->logUpdateOpenChatError(0, $error);
            }

            $this->openChatRankingPositionStore->cacheApiData($category, $apiData);
        });
    }

    private function checkKillFlag()
    {
        if (file_get_contents(AppConfig::OPEN_CHAT_API_DB_MERGER_KILL_FLAG_PATH) === '1') {
            throw new ApplicationException('強制終了しました');
        }
    }

    static function disableKillFlag()
    {
        file_put_contents(AppConfig::OPEN_CHAT_API_DB_MERGER_KILL_FLAG_PATH, '0');
    }

    static function enableKillFlag()
    {
        file_put_contents(AppConfig::OPEN_CHAT_API_DB_MERGER_KILL_FLAG_PATH, '1');
    }
}
