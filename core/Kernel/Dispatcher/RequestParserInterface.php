<?php

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

interface RequestParserInterface
{
    const AVAILABLE_REQUEST_METHOD = [
        'GET',
        'HEAD',
        'POST',
        'PUT',
        'PATCH',
        'DELETE'
    ];

    public function parse(RouteDTO $routeDto, string $requestUri);
}
