<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\LineLogin\LineLoginCallbackHandler;
use App\Services\UserAuth\LoginNewDeviceService;

class AuthPageController
{
    /**
     * LINEログインからのコールバックURL
     * https://openchat-review.me/auth/callback?return_to=
     */
    function callback(LineLoginCallbackHandler $line, LoginNewDeviceService $login, string $code, string $state)
    {
        $lineResponse = $line->handle($code, $state);
        $login->registerUserAndSessionCookie($lineResponse->open_id);

        // 前のページにリダイレクトする
        return redirect(session('return_to'));
    }
}
