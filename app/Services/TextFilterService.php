<?php

declare(strict_types=1);

namespace App\Services;

use Shadow\DB;

class TextFilterService
{
    private static ?array $wordList = null;

    private static function getWordList()
    {
        //self::$wordList = DB::execute('SELECT word, replace_string FROM word_list')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    static function filter(?string $string): string|null
    {
        if (self::$wordList === null) {
            self::getWordList();
        }

        if ($string === null) {
            return null;
        }

        return strtr($string, self::$wordList);
    }

    static function url(?string $string): string|null
    {
        if (!is_string($string)) {
            return null;
        }

        return urlencode(strtr($string, self::$wordList));
    }
}
