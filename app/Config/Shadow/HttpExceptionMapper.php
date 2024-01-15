<?php

namespace App\Config\Shadow;

class HttpExceptionMapper
{
    /**
     * Defines a mapping of HTTP errors to their corresponding HTTP status codes and messages.
     * 
     * Keys are the classes of the exceptions, and values are arrays with two elements:
     *   - httpCode: the HTTP status code to be returned
     *   - httpStatusMessage: the corresponding HTTP status message
     */
    const HTTP_ERRORS = [
        \Shared\Exceptions\NotFoundException::class =>         ['httpCode' => 404, 'log' => false, 'httpStatusMessage' => 'Not Found'],
        \Shared\Exceptions\MethodNotAllowedException::class => ['httpCode' => 405, 'log' => false, 'httpStatusMessage' => 'Method Not Allowed'],
        \Shared\Exceptions\BadRequestException::class =>       ['httpCode' => 400, 'log' => true, 'httpStatusMessage' => 'Bad Request'],
        \Shared\Exceptions\ValidationException::class =>       ['httpCode' => 400, 'log' => true, 'httpStatusMessage' => 'Bad Request'],
        \Shared\Exceptions\InvalidInputException::class =>     ['httpCode' => 400, 'log' => true, 'httpStatusMessage' => 'Bad Request'],
        \Shared\Exceptions\UploadException::class =>           ['httpCode' => 400, 'log' => true, 'httpStatusMessage' => 'Bad Request'],
        \Shared\Exceptions\SessionTimeoutException::class =>   ['httpCode' => 401, 'log' => true, 'httpStatusMessage' => 'Unauthorized'],
        \Shared\Exceptions\UnauthorizedException::class =>     ['httpCode' => 401, 'log' => true, 'httpStatusMessage' => 'Unauthorized'],
        \Shared\Exceptions\ThrottleRequestsException::class => ['httpCode' => 429, 'log' => true, 'httpStatusMessage' => 'Too Many Requests'],
    ];
}
