<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\UserAuth\LoginSessionCookieManager;

class Auth
{
    public static function check(): bool
    {
        return LoginSessionCookieManager::isLoggedinUser();
    }

    public static function id(int $default = 0): int
    {
        return LoginSessionCookieManager::getUserId() ?? $default;
    }

    public static function logout()
    {
        LoginSessionCookieManager::deleteSessionCookie();
    }
}
