<?php

declare(strict_types=1);

namespace Shadow\Kernel;

use Shadow\Kernel\RouteClasses\RouteDTO;
use Shadow\Kernel\Dispatcher\ReceptionInitializer;
use Shadow\Kernel\Dispatcher\RequestParser;
use Shadow\Kernel\Dispatcher\Routing;
use Shadow\Kernel\Dispatcher\ControllerInvoker;
use Shadow\Kernel\Dispatcher\MiddlewareInvoker;
use Shadow\Kernel\Dispatcher\RouteCallbackInvoker;
use Shadow\Exceptions\NotFoundException;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class Kernel
{
    private RouteDTO $routeDto;

    public function handle(RouteDTO $routeDto)
    {
        $this->routeDto = $routeDto;
        $this->parseRequest();
        $this->routing();
        $this->validateRequest();
        $this->callMiddleware();
        $this->callRouteCallback();
        $this->callController();
        $this->handleResponse();
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    private function parseRequest()
    {
        $request = new RequestParser;
        $request->parse($this->routeDto, $_SERVER['REQUEST_URI'] ?? '');
    }

    /**
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    private function routing()
    {
        $routing = new Routing;
        $routing->setRouteDto($this->routeDto);

        try {
            $routing->resolveController();
        } catch (NotFoundException $e) {
            if (!$this->routeDto->isDefinedRoute()) {
                throw $e;
            }

            $routing->validateAllowedMethods();
            $this->validateRequest();
            $this->callMiddleware();
            $this->callRouteCallback();
            exit;
        }

        $routing->validateAllowedMethods();
    }

    /**
     * @throws NotFoundException        If the request is GET
     * @throws ValidationException      If the request is other than GET
     * @throws \InvalidArgumentException
     */
    private function validateRequest()
    {
        $reception = new ReceptionInitializer;
        $reception->init($this->routeDto);
        $reception->callRequestValidator();
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function callMiddleware()
    {
        if (!$this->routeDto->existsMiddleware()) {
            return;
        }

        $middleware = new MiddlewareInvoker;
        $middleware->invoke($this->routeDto);
    }

    private function callRouteCallback()
    {
        $routeCallback = $this->routeDto->getRouteCallback();
        if ($routeCallback instanceof \Closure) {
            $routeCallbackInvoker = new RouteCallbackInvoker;
            $routeCallbackInvoker->invoke($this->routeDto, $routeCallback);
        } elseif ($routeCallback instanceof \Shadow\Kernel\ResponseInterface) {
            $routeCallback->send();
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function callController()
    {
        $controller = new ControllerInvoker;
        $controller->invoke($this->routeDto);
    }

    /**
     * @throws NotFoundException        If the request is GET
     * @throws BadRequestException      If the request is other than GET
     */
    private function handleResponse()
    {
        $response = new ResponseHandler;
        $response->handleResponse($this->routeDto->contlollerResponse);
    }
}
