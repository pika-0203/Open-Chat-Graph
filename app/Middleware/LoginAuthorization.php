<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\Auth;
use Shadow\Exceptions\UnauthorizedException;

class LoginAuthorization
{
    public function handle()
    {
        if (!Auth::check()) {
            throw new UnauthorizedException;
        }
    }
}
