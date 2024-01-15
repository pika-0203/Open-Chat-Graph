<?php

declare(strict_types=1);

namespace Shadow\Kernel;

/**
 * Handle sessions and flash messages.
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class Session implements SessionInterface
{
    public static function push(string|array $key, mixed $value = null)
    {
        if (is_array($key)) {
            if (!isset($_SESSION[SESSION_KEY_NAME]) || !is_array($_SESSION[SESSION_KEY_NAME])) {
                $_SESSION[SESSION_KEY_NAME] = [];
            }

            $_SESSION[SESSION_KEY_NAME] = array_merge($_SESSION[SESSION_KEY_NAME], $key);
            return;
        }

        $_SESSION[SESSION_KEY_NAME][$key] = $value;
    }

    public static function flash(string|array $key, mixed $value = null)
    {
        if (is_array($key)) {
            if (!isset($_SESSION[FLASH_SESSION_KEY_NAME]) || !is_array($_SESSION[FLASH_SESSION_KEY_NAME])) {
                $_SESSION[FLASH_SESSION_KEY_NAME] = [];
            }

            $_SESSION[FLASH_SESSION_KEY_NAME] = array_merge($_SESSION[FLASH_SESSION_KEY_NAME], $key);
            return;
        }

        $_SESSION[FLASH_SESSION_KEY_NAME][$key] = $value;
    }

    public static function remove(string $key): bool
    {
        if (isset($_SESSION[SESSION_KEY_NAME][$key])) {
            unset($_SESSION[SESSION_KEY_NAME][$key]);
            return true;
        }

        return false;
    }

    public static function flush()
    {
        Reception::$flashSession = [];
        $_SESSION[SESSION_KEY_NAME] = [];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (isset(Reception::$flashSession[$key])) {
            return Reception::$flashSession[$key];
        }

        if (isset($_SESSION[SESSION_KEY_NAME][$key])) {
            return $_SESSION[SESSION_KEY_NAME][$key];
        }

        return $default;
    }

    public static function has(string $key): bool
    {
        if (isset(Reception::$flashSession[$key])) {
            return true;
        }

        if (isset($_SESSION[SESSION_KEY_NAME][$key])) {
            return true;
        }

        return false;
    }

    public static function addError(string $key, int $code = 0, string $message = '')
    {
        $_SESSION[FLASH_SESSION_KEY_NAME]['ERRORS_ARRAY'][$key] = ['code' => $code, 'message' => $message];
    }

    public static function getError(?string $key = null): array
    {
        if ($key === null) {
            return Reception::$flashSession['ERRORS_ARRAY'] ?? [];
        }

        return Reception::$flashSession['ERRORS_ARRAY'][$key] ?? [];
    }

    public static function getErrorMessage(string $key): ?string
    {
        return Reception::$flashSession['ERRORS_ARRAY'][$key]['message'] ?? null;
    }

    public static function getErrorCode(string $key): ?int
    {
        return Reception::$flashSession['ERRORS_ARRAY'][$key]['code'] ?? null;
    }

    public static function hasError(?string $key = null): bool
    {
        if ($key === null) {
            return is_array(Reception::$flashSession['ERRORS_ARRAY'] ?? null)
                && !empty(Reception::$flashSession['ERRORS_ARRAY']);
        }

        return isset(Reception::$flashSession['ERRORS_ARRAY'][$key]);
    }

    public static function flashInput(string ...$exceptNames)
    {
        $input = Reception::$inputData;

        foreach ($exceptNames as $key) {
            if (array_key_exists($key, $input)) {
                unset($input[$key]);
            }
        }

        $_SESSION[FLASH_SESSION_KEY_NAME]['OLD_ARRAY'] = $input;
    }
}
