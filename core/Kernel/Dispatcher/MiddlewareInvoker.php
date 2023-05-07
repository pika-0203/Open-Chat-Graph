<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\Reception;
use Shadow\Kernel\ResponseHandler;
use Shadow\Kernel\ResponseHandlerInterface;
use Shadow\Kernel\RouteClasses\RouteDTO;
use Shadow\Exceptions\FailException;

class MiddlewareInvoker extends AbstractInvoker implements ClassInvokerInterface
{
    use TraitErrorResponse;

    private ResponseHandlerInterface $responseHandler;

    public function __construct(?ResponseHandlerInterface $responseHandler = null)
    {
        parent::__construct();
        $this->responseHandler = $responseHandler ?? new ResponseHandler;
    }

    public function invoke(RouteDTO $routeDto)
    {
        $this->routeFails = $routeDto->getFailsResponse();
        $this->callMiddleware($routeDto);
    }

    private function callMiddleware(RouteDTO $routeDto)
    {
        foreach ($routeDto->getMiddleware() as $middleware) {
            $className = $middleware;
            if (!method_exists($className, 'handle')) {
                throw new \InvalidArgumentException('Could not find: ' . $className . '::handle');
            }

            $methodArgs = $this->getMethodArgs($className, 'handle');

            $instance = $this->ci->constructorInjection($className);
            $middlewareResponse = $instance->handle(...$methodArgs);

            try {
                $response = $this->responseHandler->handleResponse($middlewareResponse);
            } catch (FailException $e) {
                $this->errorResponse([
                    ['key' => 'className', 'code' => $e->getCode(), 'message' => $e->getMessage()]
                ]);
            }

            if ($response === false) {
                $this->errorResponse([['key' => 'match']]);
            }

            if (is_array($response)) {
                Reception::$inputData = array_merge(Reception::$inputData, $response);
            }
        }
    }
}
