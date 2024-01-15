<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Crawler;

use App\Config\OpenChatCrawlerConfig;
use App\Config\AppConfig;
use App\Services\Crawler\CrawlerFactory;
use App\Services\OpenChat\Dto\OpenChatCrawlerDtoFactory;
use App\Services\OpenChat\Dto\OpenChatDto;

class OpenChatCrawler implements OpenChatDtoFetcherInterface
{
    private CrawlerFactory $crawlerFactory;
    private OpenChatCrawlerDtoFactory $openChatCrawlerDtoFactory;

    function __construct(
        CrawlerFactory $crawlerFactory,
        OpenChatCrawlerDtoFactory $openChatCrawlerDtoFactory
    ) {
        $this->crawlerFactory = $crawlerFactory;
        $this->openChatCrawlerDtoFactory = $openChatCrawlerDtoFactory;
    }

    /**
     * @throws \RuntimeException
     */
    function fetchOpenChatDto(string $invitationTicket): OpenChatDto|false
    {
        /**
         *  @var string $url オープンチャットの招待ページ
         *                   https://line.me/ti/g2/{$invitationTicket}
         */
        $url = AppConfig::LINE_URL . $invitationTicket;


        /**
         *  @var string $ua クローリング用のユーザーエージェント
         *                  Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36 (compatible; OpenChatStatsbot; +https://github.com/pika-0203/Open-Chat-Graph)
         */
        $ua = OpenChatCrawlerConfig::USER_AGENT;

        // クローラーを初期化
        $crawler = $this->crawlerFactory->createCrawler($url, $ua);
        if ($crawler === false) {
            return false;
        }

        // クローラーからデータを取得する
        try {
            $name = $crawler->filter(OpenChatCrawlerConfig::DOM_CLASS_NAME)->text();
            $img_url = $crawler->filter(OpenChatCrawlerConfig::DOM_CLASS_IMG)->children()->attr('src');
            $description = $crawler->filter(OpenChatCrawlerConfig::DOM_CLASS_DESCRIPTION)->text(null, false);
            $member = $crawler->filter(OpenChatCrawlerConfig::DOM_CLASS_MEMBER)->text();

            return $this->openChatCrawlerDtoFactory->validateAndMapToDto($invitationTicket, $name, $img_url, $description, $member);
        } catch (\Throwable $e) {
            throw new \RuntimeException("invitationTicket: {$invitationTicket}: " . $e->__toString());
        }
    }
}
