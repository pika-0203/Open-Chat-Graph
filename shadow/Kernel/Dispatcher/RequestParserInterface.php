<?php

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
interface RequestParserInterface
{
    const AVAILABLE_REQUEST_METHOD = [
        'GET',
        'HEAD',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS'
    ];

    public function parse(RouteDTO $routeDto, string $requestUri);
}
