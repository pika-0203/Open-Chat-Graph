<?php

declare(strict_types=1);

namespace App\Middleware;

class LocalCORS
{
    public function handle()
    {
        localCORS();
    }
}
