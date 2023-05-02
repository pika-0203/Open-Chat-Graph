<?php

declare(strict_types=1);

namespace Shadow\Kernel;

use Shadow\Kernel\RouteClasses\AbstractRoute;
use Shadow\Kernel\RouteClasses\TraitRoutePath;
use Shadow\Kernel\RouteClasses\RouteDTO;
use Shadow\Kernel\RouteClasses\RouteSecond;
use Shadow\Kernel\RouteClasses\RouteSecondInterface;
use Shadow\Kernel\RouteClasses\RouteMiddlewareGroup;
use Shadow\Kernel\RouteClasses\RouteMiddlewareGroupInterface;

/**
 * Class Route is used for defining routes and validating parameters.
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class Route extends AbstractRoute implements RouteInterface
{
    use TraitRoutePath;

    private static ?Route $instance = null;

    private function __construct(RouteDTO $routeDto)
    {
        $this->routeDto = $routeDto;
    }

    private static function create(): Route
    {
        if (self::$instance === null) {
            self::$instance = new self(new RouteDTO);
        }

        return self::$instance;
    }

    public static function path(string|array ...$path): RouteSecondInterface
    {
        $instance = self::create();
        $instance->addPath(...$path);
        return new RouteSecond($instance->routeDto);
    }

    public static function run(string ...$middlewareName)
    {
        $instance = self::create();
        $instance->routeDto->kernelMiddlewareArray = $middlewareName;

        (new Kernel)->handle($instance->routeDto);
        exit;
    }

    public static function middlewareGroup(string ...$name): RouteMiddlewareGroupInterface
    {
        $instance = self::create();
        return new RouteMiddlewareGroup($instance->routeDto, $name);
    }
}
