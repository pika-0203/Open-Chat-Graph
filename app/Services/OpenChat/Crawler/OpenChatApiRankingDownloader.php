<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Crawler;

use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use App\Config\AppConfig;

class OpenChatApiRankingDownloader
{
    private AbstractOpenChatApiRankingDownloaderProcess $openChatApiRankingDownloaderProcess;

    function __construct(AbstractOpenChatApiRankingDownloaderProcess $openChatApiRankingDownloaderProcess)
    {
        $this->openChatApiRankingDownloaderProcess = $openChatApiRankingDownloaderProcess;
    }

    /**
     * @param int $limit 一度にいくつのカテゴリを処理するか
     * 
     * @return int 全件処理するために必要な実行回数
     */
    function countMaxExecuteNum(int $limit): int
    {
        return OpenChatServicesUtility::caluclateMaxBatchNum(
            count(AppConfig::OPEN_CHAT_CATEGORY),
            $limit
        );
    }

    /**
     * @param int $limit 一度にいくつのカテゴリを処理するか
     * @param int $ExecuteNum 何度目の実行かを指定する番号 (1から始まる番号)
     * @param \Closure $callback 1件毎にループ内で呼び出すコールバック `$callback(array $apiData, string $category)`
     * @param ?\Closure $callbackByCategory 1カテゴリーごとに呼び出すコールバック `$callbackByCategory(string $category)`
     * 
     * @return int resultCount 取得したオープンチャットの件数
     * 
     * @throws \RuntimeException
     */
    function fetchOpenChatApiRankingAll(int $limit, int $ExecuteNum, \Closure $callback, ?\Closure $callbackByCategory): int
    {
        $categoryArray = array_values(AppConfig::OPEN_CHAT_CATEGORY);
        $startKey = ($ExecuteNum - 1) * $limit;
        array_splice($categoryArray, 0, $startKey);

        $resultCount = 0;
        foreach ($categoryArray as $key => $category) {
            if ($key >= $limit) {
                break;
            }

            $resultCount += $this->fetchOpenChatApiRanking((string)$category, $callback);
            if($callbackByCategory) {
                $callbackByCategory((string)$category);
            }
        }

        return $resultCount;
    }

    /**
     * @throws \RuntimeException
     */
    function fetchOpenChatApiRanking(string $category, \Closure $callback): int
    {
        $resultCount = 0;

        $ct = '0';
        while ($ct !== false) {
            $response = $this->openChatApiRankingDownloaderProcess->fetchOpenChatApiRankingProcess($category, $ct, $callback);

            if ($response === false) {
                break;
            }

            [$ct, $count] = $response;
            $resultCount += (int)$count;
        }

        return $resultCount;
    }
}
