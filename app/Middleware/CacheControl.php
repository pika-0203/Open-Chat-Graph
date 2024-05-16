<?php

declare(strict_types=1);

namespace App\Middleware;

class CacheControl
{
    public function handle()
    {
        header("Cache-Control: max-age=300, s-maxage=300, must-revalidate, public");
    }
}
