<?php

declare(strict_types=1);

namespace App\Models\Repositories;

class RepositoryCache
{
    static array $deleteOpenChat = [];

    static function addDeletedOpenChat(int $open_chat_id)
    {
        self::$deleteOpenChat[] = $open_chat_id;
    }

    static function getDeleteOpenChat()
    {
        $result = self::$deleteOpenChat;
        self::$deleteOpenChat = [];
        return $result;
    }
}
