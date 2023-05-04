<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Config\AppConfig;
use Shadow\Kernel\Validator;
use Shadow\Exceptions\NotFoundException;

class CronAuth
{
    public function handle(string $key)
    {
        Validator::str($key, regex: AppConfig::CRON_API_KEY, e: NotFoundException::class);
    }
}
