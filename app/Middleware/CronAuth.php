<?php

declare(strict_types=1);

namespace App\Middleware;

use Shadow\Kernel\Reception;
use App\Config\AdminConfig;

class CronAuth
{
    public function handle(Reception $reception, ?string $key)
    {
        if ($reception->isMethod('options')) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: PUT');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            response([])->send();
            exit;
        }

        return $key === AdminConfig::ADMIN_API_KEY;
    }
}
