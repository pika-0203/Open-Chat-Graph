<?php

declare(strict_types=1);

namespace App\Middleware;

use Shadow\Kernel\Reception;
use Shadow\Kernel\Cookie;
use Shared\Exceptions\BadRequestException;

class VerifyCsrfToken
{
    public function handle(Reception $reception)
    {
        sessinStart();

        if ($reception->isMethod('GET')) {
            Cookie::csrfToken();
            return;
        }

        $this->verifyCsrfToken();
    }

    static function verifyCsrfToken()
    {
        try {
            verifyCsrfToken();
        } catch (BadRequestException $e) {
            if (Reception::$isJson) {
                throw $e;
            }

            view('errors/invalid_cookie')->render();
            exit;
        } catch (\Exception $e) {
            Cookie::remove('CSRF-Token');
            throw $e;
        }
    }
}
