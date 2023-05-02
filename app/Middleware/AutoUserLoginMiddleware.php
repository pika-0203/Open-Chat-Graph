<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\UserAuth\AutoUserLoginService;

class AutoUserLoginMiddleware
{
    public function handle(AutoUserLoginService $user)
    {
        $user->login();
    }
}