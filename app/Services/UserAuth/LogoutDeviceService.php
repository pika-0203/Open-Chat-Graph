<?php

declare(strict_types=1);

namespace App\Services\UserAuth;

use App\Models\Repositories\UserLogoutRepositoryInterface;
use App\Models\Repositories\LogRepositoryInterface;
use App\Services\UserAuth\LoginSessionCookieManager;

class LogoutDeviceService
{
    use TraitUserDiveceTokenGenerator;

    private UserLogoutRepositoryInterface $userRepository;
    private LogRepositoryInterface $logRepository;
    private LoginSessionCookieManager $sessionCookie;

    function __construct(
        UserLogoutRepositoryInterface $userRepository,
        LogRepositoryInterface $logRepository,
        LoginSessionCookieManager $sessionCookie,
    ) {
        $this->userRepository = $userRepository;
        $this->logRepository = $logRepository;
        $this->sessionCookie = $sessionCookie;
    }

    /**
     * DBのトークン・ログインクッキー・セッションを削除してログアウトする
     */
    function logout()
    {
        $use_id = $this->sessionCookie->getUserId();

        if ($this->sessionCookie->hasDeviceToken()) {
            $token = $this->sessionCookie->getDeviceToken();
            $hash = $this->hash($token);
            $this->userRepository->deleteDeviceToken($hash);
        }

        $this->sessionCookie->deleteSessionCookie();
        $this->logRepository->logLogout($use_id, getIP(), getUA());
    }
}
