<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\Reception;
use Shadow\Kernel\ResponseHandler;
use Shadow\Kernel\ResponseHandlerInterface;
use Shadow\Kernel\RouteClasses\RouteDTO;
use Shadow\Exceptions\FailException;

class RouteCallbackInvoker extends AbstractInvoker implements RouteCallbackInvokerInterface
{
    use TraitErrorResponse;

    private ResponseHandlerInterface $responseHandler;

    public function __construct(?ResponseHandlerInterface $responseHandler = null)
    {
        parent::__construct();
        $this->responseHandler = $responseHandler ?? new ResponseHandler;
    }

    public function invoke(RouteDTO $routeDto, \Closure $routeCallback)
    {
        $this->routeFails = $routeDto->getFailsResponse();
        $this->routeCallbackValidator($routeCallback);
    }

    private function routeCallbackValidator(\Closure $routeCallback)
    {
        $closureArgs = $this->getClosureArgs($routeCallback);

        try {
            $response = $this->responseHandler->handleResponse($routeCallback(...$closureArgs));
        } catch (FailException $e) {
            $this->errorResponse([
                ['key' => 'match', 'code' => $e->getCode(), 'message' => $e->getMessage()]
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
