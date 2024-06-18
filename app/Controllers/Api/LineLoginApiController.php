<?php

namespace App\Controllers\Api;

use App\Services\Accreditation\Auth\CookieLineUserLogin;
use App\Services\Accreditation\LineLogin\LineLogin;
use App\Services\Accreditation\LineLogin\LineLoginCallbackHandler;

class LineLoginApiController
{
    function __construct()
    {
        sessionStart();
    }

    /**
     * LINEログインにリダイレクトする
     * https://openchat-review.me/auth/login?return_to=
     */
    function login(LineLogin $line, string $return_to)
    {
        // ラインのログインページにリダイレクトする
        session(compact('return_to'));
        return redirect($line->getLink(), 307);
    }

    /**
     * LINEログインからのコールバックURL
     * https://openchat-review.me/auth/callback
     */
    function callback(LineLoginCallbackHandler $line, CookieLineUserLogin $login, string $code, string $state, string $error)
    {
        $returnTo = session('return_to');
        session()->remove('return_to');

        if (!$state || !$code || $error === 'ACCESS_DENIED')
            return redirect($returnTo, 307);

        $lineResponse = $line->handle(
            $code,
            $state
        );

        $login->signIn($lineResponse->open_id);

        return redirect($returnTo, 307);
    }

    /**
     * ログアウトする
     * https://openchat-review.me/auth/logout?return_to=
     */
    function logout(LineLogin $line, CookieLineUserLogin $login, string $return_to)
    {
        $userId = $login->login();
        if ($userId) {
            $line->revoke($userId);
            $login->logout();
        }

        // 前のページにリダイレクトする
        return redirect($return_to, 307);
    }
}
