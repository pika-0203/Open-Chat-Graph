<?php

namespace App\Config;

class AppExceptionHandlerConfig 
{
    const CALLABLE_HANDLER = '\App\Exceptions\ExceptionHandler::handleException';

    // Exceptions Log directory.
    const EXCEPTION_LOG_DIRECTORY = __DIR__ . '/../app_exception.log';
}