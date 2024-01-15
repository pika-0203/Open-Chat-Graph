<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\Reception;
use Shadow\Kernel\ResponseHandler;
use Shadow\Kernel\ResponseHandlerInterface;
use Shadow\Kernel\RouteClasses\RouteDTO;
use Shared\Exceptions\FailRequestException;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class RouteCallbackInvoker extends AbstractInvoker implements RouteCallbackInvokerInterface
{
    use TraitErrorResponse;

    protected ResponseHandlerInterface $responseHandler;

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

    protected function routeCallbackValidator(\Closure $routeCallback)
    {
        $closureArgs = $this->getClosureArgs($routeCallback);

        try {
            $response = $this->responseHandler->handleResponse($routeCallback(...$closureArgs));
        } catch (FailRequestException $e) {
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
