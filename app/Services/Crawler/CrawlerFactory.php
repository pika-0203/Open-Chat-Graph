<?php

declare(strict_types=1);

namespace App\Services\Crawler;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Crawlerファクトリークラス
 */
class CrawlerFactory
{
    private CookieJar $cookieJar;

    /**
     * @param CookieJar $cookieJar CookieJarオブジェクト
     */
    public function __construct(CookieJar $cookieJar)
    {
        $this->cookieJar = $cookieJar;
    }

    /**
     * 指定されたURLにHTTPリクエストを送信し、HTTPレスポンスからCrawlerオブジェクトを生成する
     *
     * @param string $url            URL
     * @param string $userAgent      User-Agent
     * @param int $retryLimit        エラー時の再試行回数上限
     * @param int $retryInterval     エラー時の再試行間隔（秒）
     * @param string $method         リクエストメソッド
     * @param string $acceptLanguage Accept-Languageヘッダ
     * @return Crawler|false         成功した場合はCrawlerオブジェクト 404の場合はfalse
     * @throws \RuntimeException     
     */
    public function createCrawler(
        string $url,
        string $userAgent,
        int $max_redirects = 3,
        int $retryLimit = 3,
        int $retryInterval = 1,
        string $method = 'GET',
        string $acceptLanguage = 'en'
    ): Crawler|false {
        $client = new HttpBrowser(HttpClient::create(), null, $this->cookieJar);
        $client->setServerParameter('HTTP_USER_AGENT', $userAgent);
        $client->setServerParameter('HTTP_ACCEPT_LANGUAGE', $acceptLanguage);
        $client->setMaxRedirects($max_redirects);
        
        $retryCount = 0;

        try {
            while ($retryCount < $retryLimit) {
                $crawler = $client->request($method, $url);
                $response = $client->getResponse();
                $statusCode = $response->getStatusCode();
                if ($statusCode === 200) {
                    return $crawler;
                } elseif ($statusCode === 404) {
                    return false;
                } else {
                    $retryCount++;
                    sleep($retryInterval);
                }
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException(get_class($e) . ': ' . $e->getMessage());
        }

        throw new \RuntimeException($statusCode . ': ' . $url);
    }
}
