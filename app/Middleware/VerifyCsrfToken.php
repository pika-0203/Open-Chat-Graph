<?php

declare(strict_types=1);

namespace App\Middleware;

use Shadow\Kernel\Reception;
use Shadow\Kernel\Cookie;

class VerifyCsrfToken
{
    public function handle(Reception $reception)
    {
        if ($reception->isMethod('GET')) {
            Cookie::csrfToken();
        } else {
            verifyCsrfToken();
        }
    }
}
