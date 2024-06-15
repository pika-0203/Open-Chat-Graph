<?php

declare(strict_types=1);

namespace App\Services\Auth;

use Shadow\StringCryptorInterface;

class CookieUserStore
{
    private const COOKIE_EXPIRES = 3600 * 24 * 30;

    private StringCryptorInterface $cryptor;
    private string $cookieName;

    function __construct(StringCryptorInterface $cryptor, string $cookieName)
    {
        $this->cryptor = $cryptor;
        $this->cookieName = $cookieName;
    }

    /**
     * クッキーから暗号化されたユーザーIDを取得する
     *
     * @return string|null
     */
    function getEncryptedUserIdFromCookie(): ?string
    {
        return cookie($this->cookieName);
    }

    /**
     * ユーザーIDを生成する
     *
     * @return string
     */
    public function createUserId(): string
    {
        return hash('sha3-256', rand() . getIP() . time());
    }

    function makeEncryptedUserId(string $userId): array
    {
        $expires = time() + self::COOKIE_EXPIRES;

        $encryptedUserId = $this->cryptor->encryptAndHashString(
            json_encode([$expires, $userId])
        );

        return [$expires, $encryptedUserId];
    }

    /**
     * ユーザーIDを暗号化してクッキーに保存する
     *
     * @param string $userId
     */
    function saveUserIdToCookie(string $userId): void
    {
        [$expires, $encryptedUserId] = $this->makeEncryptedUserId($userId);

        cookie(
            [$this->cookieName => $encryptedUserId],
            $expires
        );
    }

    /**
     * ユーザーIDを復号する
     *
     * @return string
     * @throws \RuntimeException 復号に失敗した場合
     */
    function decryptUserId(string $encryptedUserId): string
    {
        [$expires, $userId] = json_decode($this->cryptor->verifyHashAndDecrypt($encryptedUserId), true);

        if ($this->isValidUntilHalf($expires)) {
            // 有効期限が半分以下の場合
            $this->saveUserIdToCookie($userId);
        }

        return $userId;
    }

    function isValidUntilHalf(int $expires): bool
    {
        $currentUnixTime = time();
        if ($expires <= $currentUnixTime) {
            throw new \RuntimeException("Cookieのトークンが有効期限を過ぎています。");
        } elseif ($expires <= $currentUnixTime + (self::COOKIE_EXPIRES / 2)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * クッキーから無効なユーザーIDを削除する
     */
    function removeInvalidCookie(): void
    {
        cookie()->remove($this->cookieName);
    }
}
