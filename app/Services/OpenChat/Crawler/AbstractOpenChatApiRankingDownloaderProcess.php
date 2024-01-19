<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Crawler;

use App\Services\Crawler\CrawlerFactory;
use App\Config\OpenChatCrawlerConfig;
use Shadow\Kernel\Validator;

abstract class AbstractOpenChatApiRankingDownloaderProcess
{
    /**
     * @var string $callableGenerateUrl `$callableGenerateUrl(string $category, string $ct)`
     */
    protected string $callableGenerateUrl;
    protected CrawlerFactory $crawlerFactory;

    function __construct(CrawlerFactory $crawlerFactory)
    {
        $this->crawlerFactory = $crawlerFactory;
    }

    /**
     * @return array|false `[string|false $ct, int $count]`
     */
    function fetchOpenChatApiRankingProcess(string $category, string $ct, \Closure $callback): array|false
    {
        /**
         *  @var string $url 公式サイトのランキングAPI
         *                   https://openchat.line.me/api/category/{$category}?sort=RANKING&limit=40&ct={$ct}
         *                   https://openchat.line.me/api/category/{$category}?sort=RISING&limit=40&ct={$ct}
         */
        $generateUrl = $this->callableGenerateUrl;
        $url = $generateUrl($category, $ct);

        /**
         *  @var string $ua クローリング用のユーザーエージェント
         *                  Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36 (compatible; OpenChatStatsbot; +https://github.com/pika-0203/Open-Chat-Graph)
         */
        $ua = OpenChatCrawlerConfig::USER_AGENT;

        $response = $this->crawlerFactory->createCrawler($url, $ua, getCrawler: false);
        if (!$response) {
            return false;
        }

        $apiData = json_decode($response, true);
        if (!is_array($apiData)) {
            return false;
        }

        $squares = $apiData['squaresByCategory'][0]['squares'] ?? false;
        if (!is_array($squares)) {
            return false;
        }

        $count = count($squares);
        if ($count < 1) {
            return false;
        }

        $callback($apiData, $category);

        $responseCt = Validator::str($apiData['continuationTokenMap'][$category] ?? false);

        return [$responseCt, $count];
    }
}
