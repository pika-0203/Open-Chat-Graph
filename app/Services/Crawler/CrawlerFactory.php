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
    private static float $completionTime = 0;

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
     * @param bool $getCrawler       Crawlerオブジェクトで取得するかどうか
     * 
     * @return Crawler|string|false  成功した場合はCrawlerオブジェクト 404の場合はfalse
     * 
     * @throws \RuntimeException     
     */
    public function createCrawler(
        string $url,
        string $userAgent,
        int $max_redirects = 3,
        int $retryLimit = 3,
        int $retryInterval = 1,
        string $method = 'GET',
        string $acceptLanguage = 'en',
        bool $getCrawler = true,
        array $customHeaders = []
    ): Crawler|string|false {
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }

        $client = new HttpBrowser(HttpClient::create(), null, $this->cookieJar);
        $client->setServerParameter('HTTP_USER_AGENT', $userAgent);
        $client->setServerParameter('HTTP_ACCEPT_LANGUAGE', $acceptLanguage);
        $client->setMaxRedirects($max_redirects);

        // カスタムヘッダーを設定
        foreach ($customHeaders as $header) {
            list($headerName, $headerValue) = explode(':', $header, 2);
            $client->setServerParameter('HTTP_' . strtoupper($headerName), trim($headerValue));
        }

        $retryCount = 0;

        try {
            while ($retryCount < $retryLimit) {
                $crawler = $client->request($method, $url);
                self::$completionTime = microtime(true);

                $response = $client->getResponse();
                $statusCode = $response->getStatusCode();

                if ($statusCode === 200) {
                    return $getCrawler ? $crawler : $response->getContent();
                } elseif ($statusCode === 404 || $statusCode === 400) {
                    return false;
                }

                $retryCount++;
                sleep($retryInterval);
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException(get_class($e) . ': ' . $e->getMessage());
        }

        throw new \RuntimeException($statusCode . ': ' . $url, $statusCode);
    }

    static function sleepInIntervalWithElapsedTime(int $intervalSecond)
    {
        $elapsedTime = microtime(true) - self::$completionTime;
        $waitTimeMicroseconds = max(0, ($intervalSecond - $elapsedTime) * 1000000);

        if ($waitTimeMicroseconds > 0) {
            usleep((int)$waitTimeMicroseconds);
        }
    }
}
