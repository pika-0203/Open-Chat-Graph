<?php

declare(strict_types=1);

namespace App\Services\Auth;

use Shared\Exceptions\UnauthorizedException;
use Shared\Exceptions\ValidationException;

class CookieUserLogin
{
    public function __construct(
        private CookieUserStore $cookie
    ) {
    }

    /**
     * ユーザーIDを作成または取得する
     *
     * @return string user_id
     * @throws ValidationException 復号に失敗した場合
     */
    public function login(): string
    {
        // クッキーから暗号化されたユーザーIDを取得する
        $encryptedUserId = $this->cookie->getEncryptedUserIdFromCookie();
        if (!$encryptedUserId) {
            // 新しいユーザーIDを生成する
            $userId = $this->cookie->createUserId();
            $this->cookie->saveUserIdToCookie($userId);
            return $userId;
        }

        return $this->decrypt($encryptedUserId);
    }

    /**
     * クッキーのユーザーIDを検証する
     *
     * @return string user_id
     * @throws ValidationException
     * @throws UnauthorizedException クッキーがない場合
     */
    public function verifyLogin(): string
    {
        $encryptedUserId = $this->cookie->getEncryptedUserIdFromCookie();
        if (!$encryptedUserId) {
            throw new UnauthorizedException('No user id in cookie');
        }

        return $this->decrypt($encryptedUserId);
    }

    private function decrypt(string $encryptedUserId): string
    {
        try {
            return $this->cookie->decryptUserId($encryptedUserId);
        } catch (\RuntimeException $e) {
            // 復号に失敗した場合は削除する
            $this->cookie->removeInvalidCookie();
            throw new ValidationException($e->getMessage(), 0, $e);
        }
    }
}
