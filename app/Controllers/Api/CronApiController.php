<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Config\AppConfig;

class CronApiController
{
    function index(\App\Services\OpenChat\Cron $statisticsCron)
    {
        response(['cron' => 'executed'])->send();

        fastcgi_finish_request();

        $statisticsCron->handle(
            AppConfig::CRON_UPDATE_OPENCHAT_INTERVAL,
            AppConfig::CRON_EXECUTE_COUNT
        );

        exit;
    }

    function rank(\App\Models\Repositories\StatisticsRankingUpdaterRepositoryInterface $rankingUpdater)
    {
        $resultRowCount = $rankingUpdater->updateCreateRankingTable();
        return response(['rankingUpdaterCron' => 'rowCount: ' . $resultRowCount]);
    }

    function addoc(\App\Services\OpenChat\AddOpenChat $openChat, string $url)
    {
        $isValidUrl = fn ($url) => preg_match(\App\Config\OpenChatCrawlerConfig::LINE_URL_MATCH_PATTERN, $url);

        if (!$isValidUrl($url)) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);

            $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);

            if (!$isValidUrl($url)) {
                return response([
                    'message' => '無効なURLです。',
                    'id' => null
                ]);
            }
        }

        $result = $openChat->add($url);
        return response($result);
    }
}
