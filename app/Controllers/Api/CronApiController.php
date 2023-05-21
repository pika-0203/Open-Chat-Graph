<?php

declare(strict_types=1);

namespace App\Controllers\Api;

class CronApiController
{
    function index(
        \App\Services\OpenChat\Cron $statisticsCron,
        \App\Models\Repositories\StatisticsRankingUpdaterRepositoryInterface $rankingUpdater,
        \App\Services\Statistics\OpenChatStatisticsRanking $openChatStatsRanking
    ) {
        response(['cron' => 'executed'])->send();

        fastcgi_finish_request();

        $statisticsCron->handle();

        $rankingUpdater->updateCreateRankingTable();

        $rankingList = $openChatStatsRanking->get(1, 10);
        saveArrayToFile(\App\Config\AppConfig::FILEPATH_TOP_RANKINGLIST, $rankingList + ['updatedAt' => time()]);

        exit;
    }

    function ocrowcount(\App\Models\Repositories\UpdateOpenChatRepositoryInterface $updateRepository)
    {
        $idArray = $updateRepository->getOpenChatIdByPeriod(time(), \App\Config\AppConfig::CRON_EXECUTE_COUNT);
        return response(['openChatRowCount' => count($idArray)]);
    }

    function rank(
        \App\Models\Repositories\StatisticsRankingUpdaterRepositoryInterface $rankingUpdater,
        \App\Services\Statistics\OpenChatStatisticsRanking $openChatStatsRanking
    ) {
        $resultRowCount = $rankingUpdater->updateCreateRankingTable();
        $rankingList = $openChatStatsRanking->get(1, 10);
        saveArrayToFile(\App\Config\AppConfig::FILEPATH_TOP_RANKINGLIST, $rankingList + ['updatedAt' => time()]);
        return response(['rankingUpdaterResultCount' => $resultRowCount]);
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
