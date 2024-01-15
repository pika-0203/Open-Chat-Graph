<?php

declare(strict_types=1);

namespace App\Services\Crawler;

use Symfony\Component\HttpClient\HttpClient;

class FileDownloader
{
    /**
     * 指定されたURLからファイルをダウンロードする
     *
     * @param string $url ダウンロードするファイルのURL
     * @return string|false ファイルデータ 404の場合はfalse
     * @throws \RuntimeException
     */
    public function downloadFile(
        string $url,
        string $userAgent,
        int $max_redirects = 3,
        int $retryLimit = 3,
        int $retryInterval = 1,
        string $method = 'GET',
    ): string|false {
        $httpClient = HttpClient::create();
        $response = null;
        $options =  [
            'headers' => [
                'User-Agent' => $userAgent,
            ],
            'max_redirects' => $max_redirects,
        ];

        $retryCount = 0;

        try {
            while ($retryCount < $retryLimit) {
                $response = $httpClient->request($method, $url, $options);
                $statusCode = $response->getStatusCode();
                if ($statusCode === 200) {
                    return $response->getContent();
                } elseif ($statusCode === 404) {
                    return false;
                } else {
                    $retryCount++;
                    sleep($retryInterval);
                }
            }
        } catch (\Throwable $e) {
            if ($response !== null) {
                $response->cancel();
            }

            throw new \RuntimeException(get_class($e) . ': ' . $e->getMessage());
        }

        throw new \RuntimeException($statusCode . ' Server Error: ' . $url);
    }
}
