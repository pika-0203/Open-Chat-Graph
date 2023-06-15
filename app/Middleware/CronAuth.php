<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Config\AppConfig;
use Shadow\Kernel\Validator;
use Shadow\Exceptions\NotFoundException;
use Shadow\Kernel\Reception;

class CronAuth
{
    public function handle(Reception $reception, ?string $key)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: PUT');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($reception->isMethod('options')) {
            response([])->send();
            exit;
        }

        Validator::str($key, regex: AppConfig::CRON_API_KEY, e: NotFoundException::class);
    }
}
