<?php

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

interface ReceptionInitializerInterface
{
    public function init(RouteDTO $routeDto);
    public function callRequestValidator();

    /**
     * Returns the domain and HTTP host of the current request.
     * 
     * @return string The domain and HTTP host of the current request.
     */
    public static function getDomainAndHttpHost(): string;
}