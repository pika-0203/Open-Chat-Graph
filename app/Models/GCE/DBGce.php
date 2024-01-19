<?php

declare(strict_types=1);

namespace App\Models\GCE;

use Shadow\DBInterface;
use Shadow\DB;

class DBGce extends DB implements DBInterface
{
    public static ?\PDO $pdo = null;

    /**
     * @throws \PDOException
     */
    public static function connect(string $configClass = \App\Config\Shadow\GceDatabaseConfig::class): \PDO
    {
        if (static::$pdo !== null) {
            return static::$pdo;
        }

        static::$pdo = new \PDO(
            'mysql:host=' . $configClass::HOST . ';dbname=' . $configClass::DB_NAME . ';charset=utf8mb4',
            $configClass::USER_NAME,
            $configClass::PASSWORD,
            [
                \PDO::ATTR_PERSISTENT => $configClass::ATTR_PERSISTENT,
                \PDO::MYSQL_ATTR_COMPRESS => true,
            ]
        );

        static::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return static::$pdo;
    }

    public static function fulltextSearchParam(string $keyword): string
    {
        $convertedKeyword = preg_replace('/　/u', ' ', mb_convert_encoding($keyword, 'UTF-8', 'auto'));

        if (empty(trim($convertedKeyword))) {
            throw new \InvalidArgumentException('Please provide a non-empty search keyword.');
        }

        $keywordArray = explode(' ', $convertedKeyword);
        $modifier = [];
        $modifiedWords = [];

        foreach ($keywordArray as $i => $word) {
            if ($i === 0) {
                $modifier[$i] = '+';
                $modifiedWords[$i] = $word;
                continue;
            }

            if (!isset($keywordArray[$i + 1])) {
                if (!isset($modifier[$i])) {
                    $modifier[$i] = '+';
                }

                $modifiedWords[$i] = $word;
                continue;
            }

            if ($word === 'OR') {
                $modifier[$i - 1] = '';
                $modifier[$i + 1] = '';
                continue;
            }

            if ($word === 'NOT') {
                $modifier[$i + 1] = '-';
                continue;
            }

            if (!isset($modifier[$i])) {
                $modifier[$i] = '+';
            }
            
            $modifiedWords[$i] = $word;
        }

        $param = '';
        foreach ($modifiedWords as $i => $word) {
            if ($i > 0) {
                $param .= ' ';
            }

            $param .= $modifier[$i] . static::escapeFullTextSearch($word);
        }

        return $param;
    }
}
