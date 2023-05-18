<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\LineLogin\LineLogin;
use App\Services\UserAuth\LogoutDeviceService;
use App\Services\Auth;

class AuthApiController
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
     * ログアウトする
     * https://openchat-review.me/auth/logout?return_to=
     */
    function logout(LogoutDeviceService $logout)
    {
        if (Auth::check()) {
            $logout->logout();
        }

        return redirect();
    }
}
