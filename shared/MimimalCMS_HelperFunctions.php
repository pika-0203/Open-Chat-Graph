<?php

/**
 * MimimalCMS0.1 Helper functions
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */

declare(strict_types=1);

function app()
{
    return new \Shadow\Kernel\Application();
}

/**
 * Render a template file with optional values.
 *
 * @param string|null $viewTemplateFile  Path to the template file.
 * @param array|null $valuesArray        [optional] associative array of values to pass to the template, 
 *                                       Keys starting with "_" will not be sanitized.
 * 
 * @return \Shadow\Kernel\View
 * 
 * @throws \InvalidArgumentException      If passed invalid array or not found the template file.
 */
function view(?string $viewTemplateFile = null, ?array $valuesArray = null): \Shadow\Kernel\View
{
    if ($viewTemplateFile === null && $valuesArray === null) {
        return new \Shadow\Kernel\View;
    }

    return new \Shadow\Kernel\View(\Shadow\Kernel\View::get($viewTemplateFile, $valuesArray));
}

/**
 * Returns HTTP status code and response in JSON format.
 *
 * @param array $data        The array to be returned as response.
 * @param ?int $responseCode [optional] HTTP status code
 * 
 * @return \Shadow\Kernel\Response
 */
function response(array $data, int $responseCode = 200): \Shadow\Kernel\Response
{
    return new \Shadow\Kernel\Response($responseCode, jsonData: $data);
}

/**
 * Returns HTTP status code and redirect.
 *
 * @param ?string $url      The url of path to be redirect.
 * @param int $responseCode [optional] HTTP status code
 * @return \Shadow\Kernel\Response
 */
function redirect(?string $url = null, int $responseCode = 302): \Shadow\Kernel\Response
{
    if ($url === null) {
        $url = \Shadow\Kernel\Dispatcher\ReceptionInitializer::getDomainAndHttpHost();
    } elseif (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $path = ltrim($url, "/");
        $url = \Shadow\Kernel\Dispatcher\ReceptionInitializer::getDomainAndHttpHost() . "/" . $path;
    }

    return new \Shadow\Kernel\Response($responseCode, $url);
}

/**
 * Get / set the specified session value or return a new Session instance.
 * If an array is passed as the key, an array of values will be set.
 *
 * @param  array|string|null  $key
 * @param  mixed  $default
 * 
 * @return mixed|\Shadow\Kernel\Session
 */
function session(null|string|array $value = null, mixed $default = null): mixed
{
    if ($value === null) {
        return new \Shadow\Kernel\Session;
    }

    if (is_array($value)) {
        \Shadow\Kernel\Session::push($value);
        return null;
    }

    return \Shadow\Kernel\Session::get($value, $default);
}

/**
 * Retrieve an old input value from the previous request, or return all old input as an array.
 *
 * @param string|null $key  The key of the old input value to retrieve.
 *                          If null, all old input values are returned as an array.
 * 
 * @return mixed|null|array Returns the requested old input value, or null if it does not exist. 
 *                          If $key is null, an array of all old input values is returned.
 */
function old(?string $key = null): mixed
{
    if ($key === null) {
        \Shadow\Kernel\Reception::$flashSession['OLD_ARRAY'] ?? [];
    }

    return \Shadow\Kernel\Reception::$flashSession['OLD_ARRAY'][$key] ?? null;
}

/**
 * Get / set the specified cookie value or return a new Cookie instance.
 * If an array is passed as the key, an array of values will be set.
 * 
 * @param array|string|null $value
 * @param int $expires
 * @param string $path
 * @param string $samesite
 * @param bool $secure
 * @param bool $httpOnly
 * @param string $domain
 * 
 * @return mixed|\Shadow\Kernel\Cookie
 */
function cookie(
    null|string|array $value = null,
    int $expires = 0,
    string $path = '/',
    string $samesite = COOKIE_DEFAULT_SAMESITE,
    bool $secure = COOKIE_DEFAULT_SECURE,
    bool $httpOnly = COOKIE_DEFAULT_HTTPONLY,
    string $domain = ''
): mixed {
    if ($value === null) {
        return new \Shadow\Kernel\Cookie;
    }

    if (is_array($value)) {
        \Shadow\Kernel\Cookie::push($value, null, $expires, $path, $samesite, $secure, $httpOnly, $domain);
        return null;
    }

    return \Shadow\Kernel\Cookie::get($value);
}

/**
 * 
 * Returns the absolute path to the public directory, optionally with a subdirectory appended.
 * 
 * @param string $path [optional] The path to a subdirectory within the public directory. Default is an empty string.
 * 
 * @return string      The absolute path to the public directory with the specified subdirectory appended (if provided).
 * 
 * * **Example :** Input: `publicDir()`  Output: `/var/www/public`
 * * **Example :** Input: `publicDir("css/styles.css")`  Output: `/var/www/public/css/styles.css`
 * * **Example :** Input: `publicDir("/css/styles.css")`  Output: `/var/www/public/css/styles.css`
 */
function publicDir(string $path = ''): string
{
    if ($path !== '') {
        $path = "/" . ltrim($path, "/");
    }

    return PUBLIC_DIR . $path;
}

/**
 * Returns the full URL of the current website, including the domain and optional path.
 *
 * @param string $path [optional] path to append to the domain in the URL.
 * 
 * @return string      The full URL of the current website domain.
 * 
 * * **Example :** Input: `url("home")`  Output: `https://exmaple.com/home`
 * * **Example :** Input: `url("/home")`  Output: `https://exmaple.com/home`
 */
function url(string $path = ''): string
{
    if ($path !== '') {
        $path = "/" . ltrim($path, "/");
    }

    return \Shadow\Kernel\Dispatcher\ReceptionInitializer::getDomainAndHttpHost() . $path;
}

/**
 * Generates the URL for a given page number.
 * 
 * @param string $path       The path to use.
 * @param int    $pageNumber The page number to generate the URL for. If 1, the page number is omitted.
 * 
 * @return string The URL for the given page number.
 * 
 * * **Example :** Input: `pagerUrl("home", 5)`  Output: `https://exmaple.com/home/5`
 * * **Example :** Input: `pagerUrl("/home/", 5)`  Output: `https://exmaple.com/home/5`
 * * **Example :** Input: `pagerUrl("home", 1)`  Output: `https://exmaple.com/home`
 */
function pagerUrl(string $path, int $pageNumber): string
{
    if ($path !== '') {
        $path = "/" . ltrim(rtrim($path, "/"), "/");
    }

    $secondPath = ($pageNumber > 1) ? "/" . (string) $pageNumber : '';
    return \Shadow\Kernel\Dispatcher\ReceptionInitializer::getDomainAndHttpHost() . $path . $secondPath;
}

/**
 * Create a log message from the user agent.
 * 
 * @return string User Agent.
 */
function getUA(): string
{
    $string = mb_ereg_replace('[\x00-\x1F\x7F]', '', ($_SERVER['HTTP_USER_AGENT'] ?? 'null'));
    return mb_substr($string, 0, 512);
}

/**
 * This function returns the user's IP address as a string. 
 * It first checks several HTTP headers for possible IP addresses, 
 * and then falls back to the REMOTE_ADDR server variable.
 *
 * @return string The client's IP address, or 'null' if it cannot be determined.
 */
function getIP(): string
{
    $headers = [
        'HTTP_SP_HOST',
        'HTTP_VIA',
        'HTTP_CLIENT_IP',
        'HTTP_FORWARDED',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_FROM'
    ];

    foreach ($headers as $header) {
        if (!isset($_SERVER[$header])) {
            continue;
        }

        $ips = array_map('trim', explode(',', $_SERVER[$header]));
        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? 'null';
}

/**
 * Generate a random CSRF token, save it to the session, and returns the token.
 * 
 * @return string CSRF token
 */
function getCsrfToken(): string
{
    $token = bin2hex(random_bytes(16));
    $_SESSION['_csrf'] = hash('sha256', $token);
    return $token;
}

/**
 * Generate a random CSRF token, save it to the session, and output an HTML input element containing the token.
 */
function csrfField()
{
    echo '<input type="hidden" name="_csrf" value="' . getCsrfToken() . '" />';
}

/**
 * Verify CSRF token from the session and the request in `$_POST['_csrf']` or `$_SERVER["HTTP_X_CSRF_TOKEN"]` or `$_COOKIE['CSRF-Token']`.
 *
 * @param bool $removeTokenFromSession [option]
 * @throws \Shadow\Exceptions\ValidationException         If CSRF token in the request does not matche the token in the session.
 * @throws \Shadow\Exceptions\SessionTimeoutException     If CSRF token for the session is not found.
 * @throws \LogicException              If CSRF token for the session is not string.
 */
function verifyCsrfToken(bool $removeTokenFromSession = false)
{
    // Check if CSRF token is set in the request.
    if (isset($_POST['_csrf'])) {
        $token = $_POST['_csrf'];
    } elseif (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
    } elseif (isset($_COOKIE['CSRF-Token'])) {
        $token = $_COOKIE['CSRF-Token'];
    } else {
        throw new \Shadow\Exceptions\ValidationException('CSRF token was not found on the request parameter.');
    }

    // Check if CSRF token is set in the session.
    if (!isset($_SESSION['_csrf'])) {
        throw new \Shadow\Exceptions\SessionTimeoutException('Your session has expired.');
    }

    // Get CSRF token from the session.
    $sessionToken = $_SESSION['_csrf'];
    if (!is_string($sessionToken)) {
        throw new \LogicException('CSRF token for session is not string.');
    }

    // Verify that CSRF token in the request matches the token in the session.
    $result = is_string($token) && hash_equals($sessionToken, hash('sha256', $token));
    if (!$result) {
        throw new \Shadow\Exceptions\ValidationException('Invalid CSRF token');
    }

    if ($removeTokenFromSession) {
        unset($_SESSION['_csrf']);
    }
}

/**
 * Outputs a string or number after HTML-escaping it.
 *
 * @param mixed $string The string or number to output.
 *                      Note: If the argument is not a string or number, it will not be outputted.
 * @return void
 */
function h(mixed $string): void
{
    if (is_string($string) || is_int($string) || is_float($string)) {
        echo htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Remove zero-width spaces from a string.
 *
 * @param string $str The input string.
 * @return string     The input string without zero-width spaces.
 */
function removeZWS(string $str): string
{
    $normalizedStr = \Normalizer::normalize($str, \Normalizer::FORM_KC);
    return preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $normalizedStr);
}

/**
 * Removes non-ASCII characters from the given string.
 *
 * @param string $string The input string to be cleaned.
 * @return string        The cleaned string with only ASCII characters.
 */
function sanitizeString(string $string): string
{
    return preg_replace('/[^(\x20-\x7F)]*/', '', $string);
}

/**
 * Takes a string and converts each line into a HTML paragraph.
 * 
 * @param string $string  The input string to be converted.
 * 
 * @return string         Returns the converted string with each line wrapped in a HTML paragraph.
 */
function nl2p(string $string): string
{
    $lines = explode("\n", $string);
    $result = '';
    foreach ($lines as $line) {
        $result .= '<p>' . $line . '</p>';
    }
    return $result;
}

function pre_var_dump($var)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}

function setErrorHandler()
{
    set_error_handler(function ($severity, $message, $file, $line) {
        throw new \ErrorException($message, 0, $severity, $file, $line);
    });
}

function getPerformanceCounter(string $name): string
{
    return (string) round(microtime(true) - constant($name), 3);
}

function formatBytesToMegaBytes(int $bytes, int $precision = 2): string
{
    return number_format($bytes / (1024 * 1024), $precision);
}
