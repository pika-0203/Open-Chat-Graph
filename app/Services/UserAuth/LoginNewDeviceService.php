<?php

declare(strict_types=1);

namespace App\Services\UserAuth;

use App\Models\Repositories\UserRegistrationRepositoryInterface;
use App\Models\Repositories\LogRepositoryInterface;
use App\Services\UserAuth\LoginSessionCookieManager;

class LoginNewDeviceService
{
    use TraitUserDiveceTokenGenerator;

    private UserRegistrationRepositoryInterface $userRepository;
    private LogRepositoryInterface $logRepository;
    private LoginSessionCookieManager $sessionCookie;

    function __construct(
        UserRegistrationRepositoryInterface $userRepository,
        LogRepositoryInterface $logRepository,
        LoginSessionCookieManager $sessionCookie,
    ) {
        $this->userRepository = $userRepository;
        $this->logRepository = $logRepository;
        $this->sessionCookie = $sessionCookie;
    }

    /**
     * DBにユーザーを登録して、ログインクッキー・セッションを保存する
     * 
     * @return bool `true`: 新規ユーザーが作成された場合 `false`: 既存のユーザーがログインした場合
     */
    function registerUserAndSessionCookie(string $open_id): bool
    {
        $user_id = $this->userRepository->getUserIdByOpenId($open_id);

        // トークンを生成する
        $token = $this->generateToken();
        $expires = $this->generateTokenExpires();
        $hash = $this->hash($token);

        if ($user_id === false) {
            // 新規ユーザーの場合
            $user_id = $this->userRepository->createUser($open_id, $hash, $expires);
            $this->logRepository->logCreateAccountWithOpenId($user_id, getIP(), getUA());
            $isNewUser = true;
        } else {
            // 既存ユーザーの場合
            $this->userRepository->insertDeviceToken($user_id, $hash, $expires);
            $this->logRepository->logLoginWithOpenIdSuccess($user_id, getIP(), getUA());
            $isNewUser = false;
        }

        // セッション・クッキーにログイン情報を保存する
        $this->sessionCookie->loginSessionCookie($user_id, $token, $expires);
        return $isNewUser;
    }
}
