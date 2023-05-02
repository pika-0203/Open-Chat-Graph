<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;
use Shadow\Exceptions\NotFoundException;

class ControllerInvoker extends AbstractInvoker implements ClassInvokerInterface
{
    public function invoke(RouteDTO $routeDto)
    {
        try {
            $contlollerMethodArgs = $this->getMethodArgs($routeDto->controllerClassName, $routeDto->methodName);
        } catch (\BadMethodCallException $e) {
            throw new NotFoundException($e->getMessage());
        }

        $contloller = $this->ci->constructorInjection($routeDto->controllerClassName);
        $routeDto->contlollerResponse = $contloller->{$routeDto->methodName}(...$contlollerMethodArgs);
    }
}
