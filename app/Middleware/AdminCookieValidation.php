<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\Admin\AdminAuthService;

class AdminCookieValidation
{
    public function handle(AdminAuthService $adminAuthService)
    {
        $adminAuthService->auth();
    }
}
