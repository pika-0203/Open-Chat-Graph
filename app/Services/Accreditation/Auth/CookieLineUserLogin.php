<?php

declare(strict_types=1);

namespace App\Services\Accreditation\Auth;

use App\Services\Auth\CookieUserLogin;
use App\Services\Auth\CookieUserStore;

class CookieLineUserLogin extends CookieUserLogin
{
    function __construct()
    {
        $this->cookie = app(
            CookieUserStore::class,
            ['cookieName' => 'accreditation-user-id']
        );
    }

    /**
     * ユーザーIDを取得する
     *
     * @return string user_id 未ログインの場合は空文字
     * @throws ValidationException 復号に失敗した場合
     */
    public function login(): string
    {
        // クッキーから暗号化されたユーザーIDを取得する
        $encryptedUserId = $this->cookie->getEncryptedUserIdFromCookie();
        return $encryptedUserId ? $this->decrypt($encryptedUserId) : '';
    }

    /**
     * LINEログインする
     * 
     * @throws ValidationException 復号に失敗した場合
     */
    public function signIn(string $userId): string
    {
        $this->cookie->saveUserIdToCookie($userId);
        return $userId;   
    }

    public function logout()
    {
        $this->cookie->removeInvalidCookie();
    }
}
