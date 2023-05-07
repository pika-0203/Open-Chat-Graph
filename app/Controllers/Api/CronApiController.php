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
}
