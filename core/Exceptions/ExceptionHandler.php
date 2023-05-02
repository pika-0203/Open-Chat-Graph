<?php

namespace Shadow\Exceptions;

/**
 * Registers ExceptionHandler::handleException() as the global exception handler.
 */
set_exception_handler('\Shadow\Exceptions\ExceptionHandler::handleException');

/**
 * Sets the error reporting level to include all errors.
 */
error_reporting(E_ALL);

/**
 * Registers a custom error handler that throws exceptions for all errors.
 */
set_error_handler(function ($no, $msg, $file, $line) {
    if (error_reporting() !== 0) {
        throw new \ErrorException($msg, 0, $no, $file, $line);
    }
});

/**
 * Registers a shutdown function that checks for fatal errors and
 * passes them to ExceptionHandler::handleException().
 */
register_shutdown_function(function () {
    $last = error_get_last();
    if (
        isset($last['type'])
        && boolval($last['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR))
    ) {
        ExceptionHandler::handleException(
            new \ErrorException($last['message'], 0, $last['type'], $last['file'], $last['line'])
        );
    }
});

/**
 * Exception handling and configuration class for MimimalCMS.
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class ExceptionHandler
{
    /**
     * Defines a mapping of HTTP errors to their corresponding HTTP status codes and messages.
     * 
     * Keys are the classes of the exceptions, and values are arrays with two elements:
     *   - httpCode: the HTTP status code to be returned
     *   - httpStatusMessage: the corresponding HTTP status message
     */
    const HTTP_ERRORS = [
        BadRequestException::class =>       ['httpCode' => 400, 'httpStatusMessage' => 'Bad Request'],
        ValidationException::class =>       ['httpCode' => 400, 'httpStatusMessage' => 'Bad Request'],
        InvalidInputException::class =>     ['httpCode' => 400, 'httpStatusMessage' => 'Bad Request'],
        SessionTimeoutException::class =>   ['httpCode' => 401, 'httpStatusMessage' => 'Unauthorized'],
        UnauthorizedException::class =>     ['httpCode' => 401, 'httpStatusMessage' => 'Unauthorized'],
        NotFoundException::class =>         ['httpCode' => 404, 'httpStatusMessage' => 'Not Found'],
        MethodNotAllowedException::class => ['httpCode' => 405, 'httpStatusMessage' => 'Method Not Allowed'],
        ThrottleRequestsException::class => ['httpCode' => 429, 'httpStatusMessage' => 'Too Many Requests'],
    ];

    /**
     * Handles the specified \Throwable instance.
     *
     * @param \Throwable $e The \Throwable instance to handle.
     */
    public static function handleException(\Throwable $e)
    {
        $flagName = 'App\Exceptions\ExceptionHandler::EXCEPTION_MAP';
        if (defined($flagName) && is_array($list = constant($flagName))) {
            if (array_key_exists(get_class($e), $list)) {
                \App\Exceptions\ExceptionHandler::handleException($e);
                return;
            }
        }

        // Determine whether to show detailed error information
        $flagName = 'App\Config\ExceptionHandlerConfig::EXCEPTION_HANDLER_DISPLAY_BEFORE_OB_CLEAN';
        $bool = defined($flagName) && constant($flagName);
        if ($bool && ob_get_length() > 0) {
            ob_clean();
        }

        // Handle a TestException instance
        if ($e instanceof TestException) {
            self::errorResponse($e, 'please try again later', 500, 'Internal Server Error😥');
            return;
        }

        // Handle an unhandled exception
        if (!array_key_exists(get_class($e), self::HTTP_ERRORS)) {
            self::errorResponse($e, 'please try again later', 500, 'Internal Server Error😥');
            self::errorLog($e);
            return;
        }

        // Handle a known HTTP error
        $error = self::HTTP_ERRORS[get_class($e)];
        if ($error['httpCode'] === 404 || $error['httpCode'] === 405) {
            self::errorResponse($e, '', ...$error);
            return;
        }

        // Handle other HTTP errors
        self::errorResponse($e, mb_convert_encoding($e->getMessage(), 'UTF-8'), ...$error);
        self::errorLog($e);
    }

    /**
     * Return an error response with appropriate status code and message
     *
     * @param \Throwable $e                 The exception object
     * @param string    $message           The error message to display
     * @param int       $httpCode          The HTTP status code to return
     * @param string    $httpStatusMessage The HTTP status message to return
     */
    private static function errorResponse(
        \Throwable $e,
        string $message,
        int $httpCode,
        string $httpStatusMessage,
    ) {
        // Set the HTTP response code
        http_response_code($httpCode);

        // Determine whether to show detailed error information
        $flagName = 'App\Config\ExceptionHandlerConfig::EXCEPTION_HANDLER_DISPLAY_ERROR_TRACE_DETAILS';
        $showErrorTraceFlag = defined($flagName) && constant($flagName);

        // If the request is JSON, return a JSON response
        if (self::isJsonRequest()) {
            self::jsonResponse([
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $showErrorTraceFlag ? self::getDetailsMessage($e) : $message
                ]
            ]);
            return;
        }

        // If the request is not JSON, prepare the error message for display
        $detailsMessage = $showErrorTraceFlag ? (get_class($e) . ": " . self::getDetailsMessage($e)) : $e->getMessage();
        $detailsMessage = htmlspecialchars($detailsMessage, ENT_QUOTES, 'UTF-8');

        if (ob_get_length() === false) {
            print_r($e->__toString());
            return;
        }

        // If the error page can be displayed, show it
        if (!self::showErrorPage($httpCode, $httpStatusMessage, $detailsMessage)) {
            // Otherwise, output the error message
            $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
            echo "{$httpCode} {$httpStatusMessage}<br>{$message}";
            echo "<pre>" . $detailsMessage . "</pre>";
        }
    }

    /**
     * Returns a string that contains a detailed error message with information 
     * about the file, line, and trace of the \Throwable instance.
     *
     * @param \Throwable $e The \Throwable instance to get the detailed error message from.
     * @return string      The detailed error message.
     */
    private static function getDetailsMessage(\Throwable $e): string
    {
        return mb_convert_encoding($e->getMessage(), 'UTF-8')
            . " in "
            . $e->getFile() . '(' . $e->getLine() . ')'
            . ": \n"
            . $e->getTraceAsString();
    }

    /**
     * Attempts to show a custom error page based on the HTTP code.
     * If a custom error page is not found, falls back to the generic error page.
     *
     * @param int    $httpCode          The HTTP status code.
     * @param string $httpStatusMessage The HTTP status message.
     * @param string $detailsMessage    The details message to be displayed on the error page.
     * 
     * @return bool Whether a custom error page was found and displayed.
     */
    private static function showErrorPage(int $httpCode, string $httpStatusMessage, string $detailsMessage): bool
    {
        $flagName = 'VIEWS_DIR';
        if (!defined($flagName) || !constant($flagName)) {
            return false;
        }

        $viewsDir = VIEWS_DIR;

        $filePath = $viewsDir . '/errors/' . $httpCode . '.php';
        if (file_exists($filePath)) {
            require_once $filePath;
            return true;
        }

        $filePath = $viewsDir . '/errors/error.php';
        if (file_exists($filePath)) {
            require_once $filePath;
            return true;
        }

        return false;
    }

    /**
     * Writes error messages to the error log file and exits.
     *
     * @param \Throwable $e
     */
    public static function errorLog(\Throwable $e)
    {
        // Get current date and time with timezone
        $time = date('Y-m-d H:i:s') . ' ' . date_default_timezone_get() . ': ';

        // Construct error message with class name, message and stack trace
        $message = sprintf(
            "%s: %s\n%s\n",
            get_class($e),
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // Get request headers as string
        $headerString = implode("\n", array_map(function ($key, $val) {
            if (!is_string($val)) {
                $val = var_export($val, true);
            }
            $val = str_replace("\n", '', $val);
            return "{$key}: {$val}";
        }, array_keys($_SERVER), $_SERVER));

        $flagName = 'App\Config\ExceptionHandlerConfig::EXCEPTION_LOG_DIRECTORY';
        if (!defined($flagName) || !is_writable($dir = constant($flagName))) {
            return;
        }

        // Write error log with timestamp, error message and request headers
        error_log("\n" . $time . "\n" . $message . $headerString . "\n", 3, $dir);
    }

    private static function jsonResponse(array $data)
    {
        header("Content-Type: application/json; charset=utf-8");
        ob_start('ob_gzhandler');
        echo json_encode($data);
    }

    private static function isJsonRequest(): bool
    {
        return strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false;
    }
}
