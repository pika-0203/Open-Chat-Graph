<?php

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

interface RouteCallbackInvokerInterface
{
    /**
     * Call the class method.
     */
    public function invoke(RouteDTO $routeDto, \Closure $routeCallback);
}
