<?php

use App\Services\LineLogin\LineLogin;
use App\Services\LineLogin\LineLoginCallbackHandler;
use App\Services\UserAuth\LoginNewDeviceService;
use App\Services\UserAuth\LogoutDeviceService;

class AuthPageController
{
    /**
     * LINEログインにリダイレクトする
     * https://openchat-review.me/auth/login?return_to=
     */
    function login(LineLogin $line, string $return_to)
    {
        // ラインのログインページにリダイレクトする
        return redirect($line->getLink())
            ->with('return_to', $return_to); // 前のページを保存
    }

    /**
     * LINEログインからのコールバックURL
     * https://openchat-review.me/auth/callback?return_to=
     */
    function callback(LineLoginCallbackHandler $line, LoginNewDeviceService $login, string $code, string $state)
    {
        $lineResponse = $line->handle($code, $state);
        $login->registerUserAndSessionCookie($lineResponse->open_id);

        $message = 'LINEアカウントでログインしました。';

        // 前のページにリダイレクトする
        return redirect(session('return_to'))
            ->with('message', $message);
    }

    /**
     * ログアウトする
     * https://openchat-review.me/auth/logout?return_to=
     */
    function logout(LogoutDeviceService $logout, string $return_to)
    {
        $logout->logout();
        
        $message = 'ログアウトしました。';

        // 前のページにリダイレクトする
        return redirect($return_to)
            ->with('message', $message);
    }
}
