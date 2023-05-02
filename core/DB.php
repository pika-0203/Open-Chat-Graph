<?php

declare(strict_types=1);

namespace Shadow;

use App\Config\DatabaseConfig;

/**
 * \PDO wrapper class for SQL databases
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class DB implements DBInterface
{
    public static ?\PDO $pdo = null;

    /**
     * @throws \PDOException
     */
    public static function connect()
    {
        self::$pdo = new \PDO(
            'mysql:host=' . DatabaseConfig::HOST . ';dbname=' . DatabaseConfig::DB_NAME . ';charset=utf8mb4',
            DatabaseConfig::USER_NAME,
            DatabaseConfig::PASSWORD,
            [\PDO::ATTR_PERSISTENT => DatabaseConfig::ATTR_PERSISTENT]
        );

        // Enable \PDO to throw exceptions on error.
        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public static function execute(string $query, ?array $params = null): \PDOStatement
    {
        if (self::$pdo === null) {
            self::connect();
        }

        $stmt = self::$pdo->prepare($query);

        if ($params === null) {
            $stmt->execute();
        } else {
            foreach ($params as $key => $value) {
                if ($value === null) {
                    $stmt->bindValue($key, $value, \PDO::PARAM_NULL);
                } elseif (is_bool($value)) {
                    $stmt->bindValue($key, $value, \PDO::PARAM_BOOL);
                } elseif (is_numeric($value)) {
                    $type = is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
                    $stmt->bindValue($key, $value, $type);
                } elseif (is_string($value)) {
                    $stmt->bindValue($key, $value, \PDO::PARAM_STR);
                } else {
                    throw new \InvalidArgumentException("Only string, number, null or bool is allowed: {$key}");
                }
            }

            $stmt->execute();
        }

        return $stmt;
    }

    public static function transaction(callable $callback): mixed
    {
        if (self::$pdo === null) {
            self::connect();
        }

        try {
            self::$pdo->beginTransaction();
            $result = $callback();
            self::$pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            self::$pdo->rollBack();
            throw $e;
        }
    }

    public static function fetch(string $query, ?array $params = null): array|false
    {
        if (self::$pdo === null) {
            self::connect();
        }

        return self::execute($query, $params)->fetch(\PDO::FETCH_ASSOC);
    }

    public static function fetchAll(string $query, ?array $params = null): array
    {
        if (self::$pdo === null) {
            self::connect();
        }

        return self::execute($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     *　Executes an SQL query and returns the ID of the last inserted row or sequence value.
     * 
     * @param string $query The SQL query to execute.
     * * *Example:* `'INSERT INTO user (name) SELECT :name'`
     * 
     * @param array|null $params [optional] An associative array of query parameters.
     * \InvalidArgumentException will be thrown if any of the array values are not strings or numbers.
     * * *Example:* `['name' => 'mimikyu']`
     * 
     * @return int Returns the row ID of the last row that was inserted into the database.
     * 
     * @throws \PDOException If an error occurs during the query execution.
     * @throws \InvalidArgumentException If any of the array values are not strings, numbers or bool.
     */
    public static function executeAndGetLastInsertId(string $query, ?array $params = null): int
    {
        if (self::$pdo === null) {
            self::connect();
        }

        self::execute($query, $params);
        return (int) self::$pdo->lastInsertId();
    }

    /**
     * Executes a LIKE search query and returns a \PDOStatement object with bound values.
     * 
     * @param callable $query A function that returns a string representing the SQL query. 
     * * *Example:* `fn (string $where): string => "SELECT * FROM table {$where} AND category = :category LIMIT :offset, :limit"`
     * 
     * @param callable $whereClauseQuery A function that returns a string representing the WHERE clause.
     * * *Example:* `fn (int $i): string => "(title LIKE :keyword{$i} OR text LIKE :keyword{$i})"`
     * 
     * @param string $keyword The keyword(s) to search for.
     * \InvalidArgumentException will be thrown if the string is empty or only contains whitespace characters.
     * * *Example:* `'Split keywords by whitespace and search with LIKE'`
     * 
     * @param array|null $params [optional] An associative array of query parameters.
     * \InvalidArgumentException will be thrown if any of the array values are not strings or numbers.
     * * *Example:* `['category' => 'foods', 'limit' => 20, 'offset' => 60]`
     * 
     * @return array An empty array is returned if there are zero results to search.
     * 
     * @throws \PDOException If an error occurs during the query execution.
     * @throws \LogicException If any of the given callbacks are invalid.
     * @throws \InvalidArgumentException If any of the parameter values are invalid or the given callbacks are invalid.
     */
    public static function executeLikeSearchQuery(
        callable $query,
        callable $whereClauseQuery,
        string $keyword,
        ?array $params = null
    ): array {
        if (self::$pdo === null) {
            self::connect();
        }

        $convertedKeyword = self::escapeLike(
            preg_replace('/　/u', ' ', mb_convert_encoding($keyword, 'UTF-8', 'auto'))
        );

        if (empty(trim($convertedKeyword))) {
            throw new \InvalidArgumentException('Please provide a non-empty search keyword.');
        }

        $splitKeywords = explode(' ', $convertedKeyword);

        $whereClauses = [];
        foreach ($splitKeywords as $i => $keyword) {
            $whereClauses[] = $whereClauseQuery($i);
        }

        $whereClause = 'WHERE ' . implode(' AND ', $whereClauses);

        $queryResult = $query($whereClause);
        if (!is_string($queryResult)) {
            throw new \LogicException('Query callback must return a string');
        }

        $stmt = self::$pdo->prepare($queryResult);

        $whereClausePlaceholder = 'keyword';
        foreach ($splitKeywords as $i => $word) {
            $stmt->bindValue($whereClausePlaceholder . $i, "%{$word}%", \PDO::PARAM_STR);
        }

        if ($params === null) {
            $stmt->execute();
            return $stmt;
        }

        foreach ($params as $key => $value) {
            if (!is_string($value) && !is_numeric($value)) {
                throw new \InvalidArgumentException(
                    "Invalid parameter value for key {$key}: only strings and numbers are allowed."
                );
            }

            $type = is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $type);
        }

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);;
    }

    /**
     * Escapes special characters in a string for use in LIKE clause.
     *
     * @param string $value The string to be escaped.
     * @param string $char The escape character to use (defaults to backslash).
     * @return string The escaped string.
     */
    public static function escapeLike(string $value, string $char = '\\'): string
    {
        $search  = [$char, '%', '_'];
        $replace = [$char . $char, $char . '%', $char . '_'];

        return str_replace($search, $replace, $value);
    }

    /**
     * Executes a full-text search query and returns the result as an array.
     *
     * @param callable $query A function that returns a string representing the SQL query. 
     * * *Example:* `fn (string $where): string => "SELECT * FROM table {$where} AND category = :category LIMIT :offset, :limit"`
     * 
     * @param string $whereClauseQuery The SQL query with a placeholder for the search keyword.
     * * *Example:* `'WHERE MATCH(title, text) AGAINST(:search IN BOOLEAN MODE)'`
     * 
     * @param string $keyword The search keyword to be used in the full-text search query.
     * \InvalidArgumentException will be thrown if the string is empty or only contains whitespace characters.
     * * *Example:* `'Splits keywords by whitespace'`
     * 
     * @param array|null $params [optional] An associative array of query parameters.
     * \InvalidArgumentException will be thrown if any of the array values are not strings or numbers.
     * * *Example:* `['category' => 'foods', 'limit' => 20, 'offset' => 60]`
     * 
     * @return array An array of rows returned by the query. An empty array is returned if there are no results to search.
     * 
     * @throws \PDOException If an error occurs during the query execution.
     * @throws \LogicException If any of the given callbacks are invalid.
     * @throws \InvalidArgumentException If the search keyword is empty or the WHERE clause query has an invalid placeholder.
     */
    public static function executeFulltextSearchQuery(
        callable $query,
        string $whereClauseQuery,
        string $keyword,
        ?array $params = null
    ): array {
        if (self::$pdo === null) {
            self::connect();
        }

        $convertedKeyword = preg_replace('/　/u', ' ', mb_convert_encoding($keyword, 'UTF-8', 'auto'));

        if (empty(trim($convertedKeyword))) {
            throw new \InvalidArgumentException('Please provide a non-empty search keyword.');
        }

        if (!preg_match('{:\w+}', $whereClauseQuery, $matches)) {
            throw new \InvalidArgumentException('Invalid placeholder for WHERE clause.');
        }

        $whereClausePlaceholder = $matches[0];

        $params[$whereClausePlaceholder] = '';
        foreach (explode(' ', $convertedKeyword) as $i => $word) {
            if (mb_strlen($word) < 2) {
                $word .= '*';
            }

            if ($i > 0) {
                $params[$whereClausePlaceholder] .= ' ';
            }

            $params[$whereClausePlaceholder] .= '+' . $word;
        }

        $queryResult = $query($whereClauseQuery);

        if (!is_string($queryResult)) {
            throw new \LogicException('Query callback must return a string');
        }

        return self::fetchAll($queryResult, $params);
    }
}
