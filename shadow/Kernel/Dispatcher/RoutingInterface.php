<?php

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
interface RoutingInterface
{
    const DEFAULT_CONTROLLER_CLASS_NAME = 'Index';
    const DEFAULT_CONTROLLER_METHOD_NAME = 'index';

    const API_CONTROLLER_SUFFIX = 'ApiController';
    const API_CONTROLLER_DIR = "\\App\\Controllers\\Api\\";

    const PAGE_CONTROLLER_SUFFIX = 'PageController';
    const PAGE_CONTROLLER_DIR = "\\App\\Controllers\\Pages\\";

    public function setRouteDto(RouteDTO $routeDto);
    public function resolveController();
    public function validateAllowedMethods();
}
