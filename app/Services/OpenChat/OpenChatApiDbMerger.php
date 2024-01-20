<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Services\OpenChat\Crawler\OpenChatApiRankingDownloader;
use App\Services\OpenChat\Crawler\OpenChatApiRankingDownloaderProcess;
use App\Services\OpenChat\Dto\OpenChatApiDtoFactory;
use App\Services\OpenChat\Updater\Process\OpenChatApiDbMergerProcess;
use App\Models\Repositories\Log\LogRepositoryInterface;
use App\Services\RankingPosition\Store\RankingPositionStore;
use App\Config\AppConfig;
use App\Exceptions\ApplicationException;
use App\Services\OpenChat\Dto\OpenChatDto;
use Shadow\DB;

class OpenChatApiDbMerger
{
    private OpenChatApiRankingDownloader $openChatApiRankingDataDownloader;

    function __construct(
        private OpenChatApiDtoFactory $openChatApiDtoFactory,
        private OpenChatApiDbMergerProcess $openChatApiDbMergerProcess,
        private LogRepositoryInterface $logRepository,
        private RankingPositionStore $rankingPositionStore,
        OpenChatApiRankingDownloaderProcess $openChatApiRankingDownloaderProcess,
    ) {
        $this->openChatApiRankingDataDownloader = app(
            OpenChatApiRankingDownloader::class,
            compact('openChatApiRankingDownloaderProcess')
        );
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
            // 再接続
            if (!$updateFlag) {
                DB::$pdo = null;
            }

            $this->logRepository->logUpdateOpenChatError(0, $e->__toString());

            return false;
        }

        return $count;
    }

    private function fetchOpenChatApiRankingAllProcess(int $limit, int $ExecuteNum, bool $updateFlag): int
    {
        // API OC一件ずつの処理
        $processCallback = function (OpenChatDto $apiDto) use ($updateFlag): ?string {
            $this->rankingPositionStore->addApiDto($apiDto);
            return $this->openChatApiDbMergerProcess->validateAndMapToOpenChatDtoCallback($apiDto, $updateFlag);
        };

        // API URL一件ずつの処理 
        $callback = function (array $apiData) use ($processCallback, $updateFlag): void {
            $this->checkKillFlag();

            $errors = $this->openChatApiDtoFactory->validateAndMapToOpenChatDto($apiData, $processCallback);

            foreach ($errors as $error) {
                // 再接続
                if (!$updateFlag) {
                    DB::$pdo = null;
                }

                $this->logRepository->logUpdateOpenChatError(0, $error);
            }
        };

        // API カテゴリごとの処理
        $callbackByCategory = function (string $category): void {
            $this->rankingPositionStore->saveClearCurrentCategoryApiDataCache($category);
        };

        return $this->openChatApiRankingDataDownloader->fetchOpenChatApiRankingAll($limit, $ExecuteNum, $callback, $callbackByCategory);
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
