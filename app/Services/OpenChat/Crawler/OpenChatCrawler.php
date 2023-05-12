<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Crawler;

use App\Config\OpenChatCrawlerConfig;
use App\Services\Crawler\CrawlerFactory;
use App\Services\Crawler\TraitUserAgent;
use Shadow\Kernel\Validator;

class OpenChatCrawler
{
    use TraitUserAgent;

    private CrawlerFactory $crawlerFactory;

    function __construct(CrawlerFactory $crawlerFactory)
    {
        $this->crawlerFactory = $crawlerFactory;
    }

    /**
     * オープンチャットのページからデータを取得する
     * 
     * @return array|false `['name' => string, 'img_url' => string, 'description' => string, 'member' => int]`
     * 
     * @throws \RuntimeException
     */
    function getOpenChat(string $url): array|false
    {
        $ua = 'Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36 (compatible; OpenChatStatsbot; +https://github.com/pika-0203/Open-Chat-Graph)';
        
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
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->__toString());
        }

        // 値のバリデーション
        if (Validator::str($name) === false) {
            throw new \RuntimeException('nameの値が無効です。');
        }

        if (Validator::str($img_url) === false) {
            throw new \RuntimeException('img_urlの値が無効です。');
        }

        if (Validator::str($description, emptyAble: true) === false) {
            throw new \RuntimeException('descriptionの値が無効です。');
        }

        // メンバー数を抽出する
        $member = str_replace(',', '', str_replace('Members ', '', $member));
        if (Validator::num($member) === false) {
            throw new \RuntimeException('memberの値が無効です。');
        }

        return [
            'name' => $name,
            'img_url' => $img_url,
            'description' => $description,
            'member' => (int)$member
        ];
    }
}
