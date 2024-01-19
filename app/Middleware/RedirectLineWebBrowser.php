<?php

declare(strict_types=1);

namespace App\Middleware;

class RedirectLineWebBrowser
{
    function handle()
    {
        if (!$this->isLineWebBrowser()) {
            return true;
        }

        $current_url = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($current_url, '?') !== false) {
            $new_url = $current_url . '&openExternalBrowser=1';
        } else {
            $new_url = $current_url . '?openExternalBrowser=1';
        }

        return redirect($new_url);
    }

    private function isLineWebBrowser()
    {
        /** @var string ユーザエージェント文字列 */
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        /** @var bool Lineという文字列が含まれているかを判定 */
        $isLineWebOpen = false !== strpos($userAgent, ' Line/');

        return (bool) $isLineWebOpen;
    }
}
