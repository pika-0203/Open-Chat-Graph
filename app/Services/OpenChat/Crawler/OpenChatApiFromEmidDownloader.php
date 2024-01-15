<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Crawler;

use App\Services\Crawler\CrawlerFactory;
use App\Config\OpenChatCrawlerConfig;
use App\Services\OpenChat\Dto\OpenChatApiFromEmidDtoFactory;
use App\Services\OpenChat\Dto\OpenChatDto;

class OpenChatApiFromEmidDownloader implements OpenChatDtoFetcherInterface
{
    private CrawlerFactory $crawlerFactory;
    private OpenChatApiFromEmidDtoFactory $openChatApiFromEmidDtoFactory;

    function __construct(
        CrawlerFactory $crawlerFactory,
        OpenChatApiFromEmidDtoFactory $openChatApiFromEmidDtoFactory
    ) {
        $this->crawlerFactory = $crawlerFactory;
        $this->openChatApiFromEmidDtoFactory = $openChatApiFromEmidDtoFactory;
    }

    /**
     * @return array|false 取得したオープンチャット
     * 
     * @throws \RuntimeException
     */
    private function fetchFromEmid(string $emid): array|false
    {
        /**
         *  @var string $url 公式サイトのオープンチャット取得API
         *                   https://openchat.line.me/api/square/{$emid}?limit=1
         */
        $url = OpenChatCrawlerConfig::generateOpenChatApiOcDataFromEmidUrl($emid);

        /**
         *  @var array $headers データ取得時に必要なヘッダー情報
         *                      X-Line-Seo-User: x9bfc33ffe50854cf0d446a6013cf1824
         */
        $headers = OpenChatCrawlerConfig::OPEN_CHAT_API_OC_DATA_FROM_EMID_DOWNLOADER_HEADER;

        /**
         *  @var string $ua クローリング用のユーザーエージェント
         *                  Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36 (compatible; OpenChatStatsbot; +https://github.com/pika-0203/Open-Chat-Graph)
         */
        $ua = OpenChatCrawlerConfig::USER_AGENT;

        $response = $this->crawlerFactory->createCrawler($url, $ua, customHeaders: $headers, getCrawler: false);

        if (!$response) {
            return false;
        }

        $responseArray = json_decode($response, true);
        if (!is_array($responseArray)) {
            return false;
        }

        return $responseArray;
    }

    /**
     * @return array|string `['invitationTicket' => string]`  
     *                      戻り値がstringの場合は例外のメッセージ
     */
    function fetchOpenChatApiFromEmidDtoElement(string $emid): array|string
    {
        try {
            $response = $this->fetchFromEmid($emid);
            if (!$response) {
                return "400 or 404エラー: emid: {$emid}";
            }

            return $this->openChatApiFromEmidDtoFactory->validateAndMapToOpenChatApiFromEmidDtoElementArray($response);
        } catch (\RuntimeException $e) {
            return $e->getMessage() . ": emid: {$emid}";
        }
    }

    /**
     *@throws \RuntimeException
     */
    function fetchOpenChatDto(string $emid): OpenChatDto|false
    {
        $response = $this->fetchFromEmid($emid);
        if (!$response) {
            return false;
        }

        return $this->openChatApiFromEmidDtoFactory->validateAndMapToOpenChatApiFromEmidDto($response);
    }
}
