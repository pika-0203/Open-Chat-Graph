<?php

declare(strict_types=1);

namespace App\Services\Accreditation\Auth;

use App\Services\Auth\CookieUserLogin;
use App\Services\Auth\CookieUserStore;
use Shared\Exceptions\ValidationException;

class CookieLineUserLogin extends CookieUserLogin
{
    private const LINE_OPEN_ID_PATTERN = '/^U[0-9A-Fa-f]{32}$/';

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
        if(!$encryptedUserId)
            return '';

        $userId = $this->decrypt($encryptedUserId);

        if (!preg_match(self::LINE_OPEN_ID_PATTERN, $userId)) {
            $this->cookie->removeInvalidCookie();
            throw new ValidationException('識別子のパターンが一致しません');
        }

        return $userId;
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
