<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\Reception;
use Shadow\Kernel\Session;
use Shadow\Kernel\ResponseInterface;
use Shadow\Exceptions\NotFoundException;
use Shadow\Exceptions\InvalidInputException;

trait TraitErrorResponse
{
    protected ?ResponseInterface $routeFails = null;

    /**
     * Generate error response.
     *
     * @param array $errorArray List of error details, each containing 'key', 'code', and 'message'.
     * @throws NotFoundException
     * @throws InvalidInputException
     */
    protected function errorResponse(array $errorArray)
    {
        if ($this->routeFails !== null) {
            foreach ($errorArray as $error) {
                Session::addError($error['key'], $error['code'], $error['message']);
            }

            $this->routeFails->send();
            exit;
        }

        $message = $errorArray[0]['message'] ?? 'Request validation failed.';
        $code = $errorArray[0]['code'] ?? 0;

        if (Reception::$requestMethod === 'GET') {
            throw new NotFoundException($message, $code);
        } else {
            throw new InvalidInputException($message, $code);
        }
    }
}
