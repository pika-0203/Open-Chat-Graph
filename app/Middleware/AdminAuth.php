<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\Admin\AdminAuthService;

class AdminAuth
{
    function handle(AdminAuthService $adminAuthService)//: bool
    {
        //return $adminAuthService->auth();
        $adminAuthService->auth();
    }
}
