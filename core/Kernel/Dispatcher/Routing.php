<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;
use Shadow\Exceptions\NotFoundException;
use Shadow\Exceptions\MethodNotAllowedException;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class Routing implements RoutingInterface
{
    private RouteDTO $routeDto;

    public function setRouteDto(RouteDTO $routeDto)
    {
        $this->routeDto = $routeDto;
    }

    /**
     * @throws NotFoundException
     */
    public function resolveController()
    {
        $explicitController = $this->routeDto->getExplicitControllerArray();
        if (!$explicitController) {
            $this->validatePath();
            $this->getDynamicControllerName();
        } else {
            $this->getExplicitControllerName($explicitController);
        }
    }

    private function getExplicitControllerName(array $explicitController)
    {
        $this->routeDto->controllerClassName = $explicitController[0];
        $this->routeDto->methodName = $explicitController[1];
    }

    private function validatePath()
    {
        $paths = $this->routeDto->parsedPathArray;

        if ($paths[0] !== '' && preg_grep('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $paths) === []) {
            throw new NotFoundException(
                "Invalid path: It must starts with letter or underscore, followed by any number of letters, numbers, or underscores."
            );
        }

        // If there is a 3rd path, return 404 error
        if (count($paths) > 2) {
            throw new NotFoundException('Three or more Paths are not supported without parametars.');
        }
    }

    private function getDynamicControllerName()
    {
        // Set default controller name
        if ($this->routeDto->requestMethod !== 'GET' || $this->routeDto->isJson) {
            $controllerSuffix = RoutingInterface::API_CONTROLLER_SUFFIX;
            $controllerDir =    RoutingInterface::API_CONTROLLER_DIR;
        } else {
            $controllerSuffix = RoutingInterface::PAGE_CONTROLLER_SUFFIX;
            $controllerDir =    RoutingInterface::PAGE_CONTROLLER_DIR;
        }

        // Resolve controller name
        if ($this->routeDto->parsedPathArray[0] !== '') {
            $controllerPrefix = ucfirst($this->routeDto->parsedPathArray[0]);
            $this->routeDto->controllerClassName = $controllerDir . $controllerPrefix . $controllerSuffix;
        } else {
            $this->routeDto->controllerClassName = $controllerDir . RoutingInterface::DEFAULT_CONTROLLER_CLASS_NAME . $controllerSuffix;
        }

        // Resolve method name
        if (isset($this->routeDto->parsedPathArray[1])) {
            $this->routeDto->methodName = $this->routeDto->parsedPathArray[1];
        } else {
            $this->routeDto->methodName = RoutingInterface::DEFAULT_CONTROLLER_METHOD_NAME;
        }

        if (class_exists($this->routeDto->controllerClassName)) {
            if (!method_exists($this->routeDto->controllerClassName, $this->routeDto->methodName)) {
                throw new NotFoundException('Could not find controller method.');
            }
        } else {
            throw new NotFoundException('Could not find controller file');
        }
    }

    /**
     * @throws MethodNotAllowedException
     */
    public function validateAllowedMethods()
    {
        $allowedMethod = $this->routeDto->routeRequestMethodArray;

        if (
            $this->routeDto->requestMethod === 'GET'
            && ($allowedMethod === false || in_array('GET', $allowedMethod, true))
        ) {
            return;
        }

        if (
            $this->routeDto->requestMethod === 'HEAD'
            && ($allowedMethod === false || in_array(['GET', 'HEAD'], $allowedMethod, true))
        ) {
            exit;
        }

        if ($allowedMethod === false || !in_array($this->routeDto->requestMethod, $allowedMethod, true)) {
            $message = ($allowedMethod === false) ? 'GET, HEAD (default)' : implode(', ', $allowedMethod);
            throw new MethodNotAllowedException('Allowed request methods: ' . $message);
        }
    }
}
