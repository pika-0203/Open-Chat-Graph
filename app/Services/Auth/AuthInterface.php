<?php

declare(strict_types=1);

namespace App\Services\Auth;

interface AuthInterface
{
    function loginCookieUserId(): string;
    function verifyCookieUserId(): string;
}
