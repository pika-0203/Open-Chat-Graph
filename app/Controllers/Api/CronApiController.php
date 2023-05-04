<?php

use App\Config\AppConfig;

class CronApiController
{
    function index(App\Services\OpenChat\Cron $statisticsCron)
    {
        response(['cron' => 'executed'])->send();

        fastcgi_finish_request();

        $statisticsCron->handle(
            AppConfig::CRON_UPDATE_OPENCHAT_INTERVAL,
            AppConfig::CRON_EXECUTE_COUNT
        );

        exit;
    }

    function rank(App\Models\Repositories\StatisticsRankingUpdaterRepositoryInterface $rankingUpdater)
    {
        $rankingUpdater->updateCreateRankingTable(AppConfig::OPEN_CHAT_RANKING_LIMIT * 5);
        return response(['rankingUpdaterCron' => 'done']);
    }
}
