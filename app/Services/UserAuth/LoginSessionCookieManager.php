<?php

declare(strict_types=1);

namespace App\Services\UserAuth;

use Shadow\Kernel\Validator;

class LoginSessionCookieManager
{
    /**
     * ログイン済ユーザーであるかの検証
     */
    public static function isLoggedinUser(): bool
    {
        return Validator::num(session('user_id')) !== false;
    }

    /**
     * セッションからユーザーIDを取得する
     */
    public static function getUserId(): ?int
    {
        return session('user_id');
    }

    /**
     * セッション・クッキーのログイン情報を削除する
     */
    public static function deleteSessionCookie()
    {
        session()->remove('user_id');
        cookie()->remove('token');
    }

    /**
     * セッション・クッキーにログイン情報を保存する
     */
    public function loginSessionCookie(int $user_id, string $token, int $expires)
    {
        session_regenerate_id(true);
        session(['user_id' => $user_id]);
        cookie(['token' => $token], $expires);
    }

    /**
     * セッションにユーザーIDを保存する
     */
    public function loginSession(int $user_id)
    {
        session_regenerate_id(true);
        session(['user_id' => $user_id]);
    }

    /**
     * ユーザーのクッキーにログイン情報があるかの検証
     */
    public function hasDeviceToken(): bool
    {
        return Validator::str(cookie('token')) !== false;
    }

    /**
     * クッキーからトークンを取得する
     */
    public function getDeviceToken(): ?string
    {
        return cookie('token');
    }
}
