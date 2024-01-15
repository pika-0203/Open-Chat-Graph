<?php

namespace App\Services\OpenChat\Utility;

class OpenChatServicesUtility
{
    /**
     * @return bool 収集を拒否している場合は true
     */
    static function containsHashtagNolog(string $desc): bool
    {
        return strpos($desc, '#nolog') !== false;
    }

    static function caluclateMaxBatchNum(int $target, int $limit): int
    {
        return (int)ceil($target / $limit);
    }
}
