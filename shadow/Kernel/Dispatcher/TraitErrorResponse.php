<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\Reception;
use Shadow\Kernel\Session;
use Shadow\Kernel\ResponseInterface;
use Shared\Exceptions\NotFoundException;
use Shared\Exceptions\InvalidInputException;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
trait TraitErrorResponse
{
    protected ResponseInterface|false|null $routeFails;

    /**
     * Generate error response.
     *
     * @param array $errorArray List of error details, each containing 'key', 'code', and 'message'.
     * @throws NotFoundException
     * @throws InvalidInputException
     */
    protected function errorResponse(array $errorArray, ?string $exeptionClass = null)
    {
        if ($this->routeFails !== null) {
            foreach ($errorArray as $error) {
                Session::addError($error['key'], $error['code'], $error['message']);
            }

            if ($this->routeFails === false) {
                return;
            }

            $this->routeFails->send();
            exit;
        } else {
            $message = $errorArray[0]['message'] ?? 'Request validation failed.';
            $code = $errorArray[0]['code'] ?? 0;

            if ($exeptionClass) {
                throw new $exeptionClass($message, $code);
            }

            if (Reception::$requestMethod === 'GET') {
                throw new NotFoundException($message, $code);
            } else {
                throw new InvalidInputException($message, $code);
            }
        }
    }
}
