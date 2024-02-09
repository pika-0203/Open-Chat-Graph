<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Crawler;

use App\Config\AppConfig;

class OpenChatApiRankingDownloader
{
    function __construct(
        private AbstractOpenChatApiRankingDownloaderProcess $openChatApiRankingDownloaderProcess
    ) {
    }

    /**
     * @param \Closure $callback 1件毎にループ内で呼び出すコールバック `$callback(array $apiData, string $category)`
     * @param ?\Closure $callbackByCategory 1カテゴリーごとに呼び出すコールバック `$callbackByCategory(string $category)`
     * 
     * @return array{ count: int, category: string, dateTime: \DateTime }[] 取得済件数とカテゴリ
     * 
     * @throws \RuntimeException
     */
    function fetchOpenChatApiRankingAll(\Closure $callback, ?\Closure $callbackByCategory): array
    {
        $result = [];
        foreach (AppConfig::OPEN_CHAT_CATEGORY as $key => $category) {
            $count = $this->fetchOpenChatApiRanking((string)$category, $callback);
            if ($callbackByCategory) {
                $callbackByCategory((string)$category);
            }

            $result[] = ['count' => $count, 'category' => $key, 'dateTime' => new \DateTime()]; 
        }

        return $result;
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
