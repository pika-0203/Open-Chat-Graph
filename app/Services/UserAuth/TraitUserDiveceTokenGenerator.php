<?php

declare(strict_types=1);

namespace App\Services\UserAuth;

use App\Config\AppConfig;

trait TraitUserDiveceTokenGenerator
{
    /**
     * デバイストークンを生成する  
     * 
     * @return string SHA256ハッシュ
     */
    private function generateToken(): string
    {
        // ランダムなトークンを生成
        return hash('sha256', getIP() . time() . rand());
    }

    /**
     * 有効期限を発行する
     * 
     * @return int Unixtime  
     */
    private function generateTokenExpires(): int
    {
        // トークンの有効期限を設定
        return time() + AppConfig::DEVICE_COOKIE_EXPIRES;
    }

    /**
     * ハッシュを生成する  
     * 
     * @return string SHA256ハッシュ
     */
    private function hash(string $string): string
    {
        return hash('sha256', $string);
    }
}
