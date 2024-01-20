<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Crawler;

use App\Config\AppConfig;
use App\Exceptions\ApplicationException;
use App\Models\Repositories\Log\LogRepositoryInterface;
use App\Services\OpenChat\Crawler\OpenChatApiRankingDownloader;
use App\Services\OpenChat\Crawler\OpenChatApiRisingDownloaderProcess;
use App\Services\OpenChat\Dto\OpenChatApiDtoFactory;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\RankingPosition\Store\RisingPositionStore;

class RisingPositionCrawling
{
    private const FETCH_OPEN_CHAT_API_RANKING_ALL_ARG = [100, 1]; // 全カテゴリ取得
    private OpenChatApiRankingDownloader $openChatApiRisingDataDownloader;

    function __construct(
        private RisingPositionStore $risingPositionStore,
        private OpenChatApiDtoFactory $openChatApiDtoFactory,
        private LogRepositoryInterface $logRepository,
        OpenChatApiRisingDownloaderProcess $openChatApiRankingDownloaderProcess,
    ) {
        $this->openChatApiRisingDataDownloader = app(
            OpenChatApiRankingDownloader::class,
            compact('openChatApiRankingDownloaderProcess')
        );
    }

    function risingPositionCrawling(): void
    {
        $this->disableKillFlag();

        // API OC一件ずつの処理
        $processCallback = function (OpenChatDto $apiDto): ?string {
            $this->risingPositionStore->addApiDto($apiDto);
            return null;
        };

        // API URL一件ずつの処理
        $callback = function (array $apiData) use ($processCallback): void {
            $this->checkKillFlag();

            $errors = $this->openChatApiDtoFactory->validateAndMapToOpenChatDto($apiData, $processCallback);

            foreach ($errors as $error) {
                $this->logRepository->logUpdateOpenChatError(0, $error);
            }
        };

        // API カテゴリごとの処理
        $callbackByCategory = function (string $category): void {
            $this->risingPositionStore->saveClearCurrentCategoryApiDataCache($category);
        };

        $this->openChatApiRisingDataDownloader->fetchOpenChatApiRankingAll(
            ...[
                ...self::FETCH_OPEN_CHAT_API_RANKING_ALL_ARG,
                $callback,
                $callbackByCategory
            ]
        );
    }

    private function checkKillFlag()
    {
        if (file_get_contents(AppConfig::OPEN_CHAT_API_DB_MERGER_KILL_FLAG_PATH) === '1') {
            throw new ApplicationException('強制終了しました');
        }
    }

    private function disableKillFlag()
    {
        file_put_contents(AppConfig::OPEN_CHAT_API_DB_MERGER_KILL_FLAG_PATH, '0');
    }
}
