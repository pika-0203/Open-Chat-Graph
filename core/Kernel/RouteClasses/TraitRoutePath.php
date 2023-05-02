<?php

declare(strict_types=1);

namespace Shadow\Kernel\RouteClasses;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
trait TraitRoutePath
{
    protected function addPath(string|array ...$path)
    {
        if (!is_string($path[0] ?? null)) {
            throw new \InvalidArgumentException('The first argument must be a string representing the path.');
        }

        if ($path[0] === '/') {
            $path[0] = '';
        }

        $this->routeDto->routePathArray[] = $path[0];
        if (count($path) === 1) {
            return $this;
        }

        unset($path[0]);
        $key = array_key_last($this->routeDto->routePathArray);
        foreach ($path as $controller) {
            if (!is_array($controller) || !isset($controller[0], $controller[1])) {
                throw new \InvalidArgumentException(
                    'The second argument and later must be an array with two or more string elements that include a controller class name and an action method name.'
                );
            }

            $requestMethod = isset($controller[2]) ? strtoupper($controller[2]) : $this->routeDto->requestMethod;
            $this->routeDto->routeExplicitControllerArray[$key][$requestMethod] = [$controller[0], $controller[1]];
        }
    }
}
