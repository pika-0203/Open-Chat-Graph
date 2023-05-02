<?php

use App\Services\OpenChat\Cron;
use App\Config\AppConfig;

class CronApiController
{
    private Cron $cron;

    function __construct(Cron $cron)
    {
        $this->cron = $cron;
    }

    function index()
    {
        response(['cron' => 'executed'])->send();
        fastcgi_finish_request();

        $this->cron->handle(
            AppConfig::CRON_UPDATE_OPENCHAT_INTERVAL,
            AppConfig::CRON_EXECUTE_COUNT
        );

        exit;
    }
}
