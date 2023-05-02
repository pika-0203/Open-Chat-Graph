<?php

declare(strict_types=1);

namespace App\Services\UserAuth;

use App\Models\Repositories\UserLoginRepositoryInterface;
use App\Models\Repositories\LogRepositoryInterface;
use App\Services\UserAuth\LoginSessionCookieManager;
use App\Exceptions\InvalidTokenException;
use App\Config\AppConfig;

class AutoUserLoginService
{
    use TraitUserDiveceTokenGenerator;

    private UserLoginRepositoryInterface $userRepository;
    private LogRepositoryInterface $logRepository;
    private LoginSessionCookieManager $sessionCookie;

    function __construct(
        UserLoginRepositoryInterface $userRepository,
        LogRepositoryInterface $logRepository,
        LoginSessionCookieManager $sessionCookie,
    ) {
        $this->userRepository = $userRepository;
        $this->logRepository = $logRepository;
        $this->sessionCookie = $sessionCookie;
    }

    /**
     * 自動ログインをする
     * 
     * @return bool 自動ログインができたかどうか
     * 
     * @throws InvalidTokenException
     */
    function login(): bool
    {
        if ($this->sessionCookie->isLoggedinUser()) {
            // ログイン済みの場合
            return true;
        } else if ($this->sessionCookie->hasDeviceToken()) {
            // クッキーにトークンがある場合
            return $this->verfyTokenAndLogin();
        } else {
            return false;
        }
    }

    private function verfyTokenAndLogin(): bool
    {
        $hash = $this->hash($this->sessionCookie->getDeviceToken());
        $user = $this->userRepository->getUserIdByToken($hash);

        if ($user === false) {
            throw new InvalidTokenException('Cookieのトークンが無効です。');
        }

        if ($this->isValidUntilHalf($user['expires'])) {
            // 有効期限が半分以下の場合
            $this->updateToken($user['user_id'], $hash);
        } else {
            // 有効期限が半分以上の場合
            $this->sessionCookie->loginSession($user['user_id']);
        }

        $this->logRepository->logLoginSuccess($user['user_id'], getIP(), getUA());
        return true;
    }

    private function isValidUntilHalf(int $expires): bool
    {
        $currentUnixTime = time();
        if ($expires <= $currentUnixTime) {
            throw new InvalidTokenException("Cookieのトークンが有効期限を過ぎています。");
        } elseif ($expires <= $currentUnixTime + (AppConfig::DEVICE_COOKIE_EXPIRES / 2)) {
            return true;
        } else {
            return false;
        }
    }

    private function updateToken(int $user_id, string $hash)
    {
        // トークンを生成する
        $newToken = $this->generateToken();
        $newExpires = $this->generateTokenExpires();
        $newHash = $this->hash($newToken);

        $this->userRepository->updateDeviceToken($hash, $newHash, $newExpires);
        $this->sessionCookie->loginSessionCookie($user_id, $newToken, $newExpires);
    }
}
