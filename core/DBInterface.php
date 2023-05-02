<?php

namespace Shadow;

interface DBInterface
{
    /**
     * Executes an SQL query and returns a PDOStatement object with bound values.
     *
     * @param string     $query  The SQL query to execute.
     *                   * *Example:* `'SELECT * FROM table WHERE category = :category LIMIT :offset, :limit'`
     * 
     * @param array|null $params [optional] An associative array of query parameters.
     *                           InvalidArgumentException will be thrown if any of the array values are not strings or numbers.
     *                   * *Example:* `['category' => 'foods', 'limit' => 20, 'offset' => 60]`
     * 
     * @return PDOStatement Returns a PDOStatement object containing the results of the query, or false.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the array values are not strings, numbers or bool.
     */
    public static function execute(string $query, ?array $params = null): \PDOStatement;

    /**
     * Executes a callback function within a transaction.
     * Rolls back the transaction and throws an exception on failure.
     *
     * @param callable $callback The callback function to execute.
     * @return mixed The return value of the callback function.
     * @throws \Throwable
     */
    public static function transaction(callable $callback): mixed;

    /**
     * Executes an SQL query and returns a single row as an associative array.
     * 
     * @param string     $query  The SQL query to execute.
     *                   * *Example:* `'SELECT * FROM table WHERE id = :id'`
     * 
     * @param array|null $params [optional] An associative array of query parameters.
     *                           InvalidArgumentException will be thrown if any of the array values are not strings or numbers.
     *                   * *Example:* `['id' => 10]`
     * 
     * @return array|false Returns a single row as an associative array or false if no rows.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the array values are not strings, numbers or bool.
     */
    public static function fetch(string $query, ?array $params = null): array|false;

    /**
     * Executes an SQL query and returns rows as associative arrays.
     * 
     * @param string     $query  The SQL query to execute.
     *                   * *Example:* `'SELECT * FROM table WHERE category = :category LIMIT :offset, :limit'`
     * 
     * @param array|null $params [optional] An associative array of query parameters.
     *                           InvalidArgumentException will be thrown if any of the array values are not strings or numbers.
     *                   * *Example:* `['category' => 'foods', 'limit' => 20, 'offset' => 60]`
     * 
     * @return array An empty array is returned if there are zero results to fetch.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the array values are not strings, numbers or bool.
     */
    public static function fetchAll(string $query, ?array $params = null): array;
}
