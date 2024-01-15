<?php

declare(strict_types=1);

namespace App\Exceptions\Handlers;

use Shadow\ApplicationExceptionHandlerInterface;

class ApplicationExceptionHandler implements ApplicationExceptionHandlerInterface
{
    const EXCEPTION_MAP = [
        \App\Exceptions\ApplicationException::class => 'app',
    ];

    public static function handleException(\Throwable $e, string $className)
    {
        echo self::EXCEPTION_MAP[$className] . ': ';
        echo $e->getMessage();
    }
}
