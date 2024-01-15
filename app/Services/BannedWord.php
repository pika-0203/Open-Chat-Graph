<?php

declare(strict_types=1);

namespace App\Services;

class BannedWord
{
    static array $bannedNameWords = [
        '死ね',
        '殺す',
        '殺害',
        '殺し',
    ];

    static function replaceName(string $string): string
    {
        return self::replace($string, self::$bannedNameWords);
    }

    private static function replace(string $string, array $words): string
    {
        $cleanString = mb_convert_kana(mb_strtolower($string), 'asHVc');

        $words = array_map(fn ($str) => preg_quote($str, '/'), $words);

        $pattern = '/(' . implode('|', $words) . ')/u';

        $string = preg_replace_callback(
            $pattern,
            fn ($matches) => str_repeat('*', mb_strlen($matches[0])),
            $cleanString
        );

        return $string;
    }
}
