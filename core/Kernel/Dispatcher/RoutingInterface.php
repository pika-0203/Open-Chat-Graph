<?php

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

interface RoutingInterface
{
    const DEFAULT_CONTROLLER_CLASS_NAME = 'Index';
    const DEFAULT_CONTROLLER_METHOD_NAME = 'index';

    const API_CONTROLLER_SUFFIX = 'ApiController';
    const API_CONTROLLER_DIR = __DIR__ . '/../../../app/Controllers/Api';

    const PAGE_CONTROLLER_SUFFIX = 'PageController';
    const PAGE_CONTROLLER_DIR = __DIR__ . '/../../../app/Controllers/Pages';

    public function setRouteDto(RouteDTO $routeDto);
    public function validatePath();
    public function resolveController();
    public function validateAllowedMethods();
}