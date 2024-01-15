<?php

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
interface RouteCallbackInvokerInterface
{
    /**
     * Call the class method.
     */
    public function invoke(RouteDTO $routeDto, \Closure $routeCallback);
}
