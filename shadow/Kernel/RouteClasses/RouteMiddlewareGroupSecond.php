<?php

declare(strict_types=1);

namespace Shadow\Kernel\RouteClasses;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class RouteMiddlewareGroupSecond extends RouteSecond implements RouteMiddlewareGroupSecondInterface
{
    use TraitMiddlewarePath;

    protected array $middlewareGroup;

    public function __construct(RouteDTO &$routeDto, array $middlewareGroup)
    {
        $this->routeDto = $routeDto;
        $this->middlewareGroup = $middlewareGroup;
    }
}
