<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\Reception;
use Shadow\Kernel\ResponseHandler;
use Shadow\Kernel\ResponseHandlerInterface;
use Shadow\Kernel\RouteClasses\RouteDTO;
use Shadow\Exceptions\ValidationException;
use Shadow\Exceptions\NotFoundException;
use Shadow\Exceptions\BadRequestException;

class RouteCallbackInvoker extends AbstractInvoker implements RouteCallbackInvokerInterface
{
    use TraitErrorResponse;

    private ResponseHandlerInterface $responseHandler;

    public function __construct(?ResponseHandlerInterface $responseHandler = null)
    {
        parent::__construct();
        $this->responseHandler = $responseHandler ?? new ResponseHandler;
    }

    public function invoke(RouteDTO $routeDto, \Closure $routeCallback): array
    {
        $this->routeFails = $routeDto->getFailsResponse();

        $callbackValidatedArray = $this->routeCallbackValidator($routeCallback);
        if (empty($callbackValidatedArray)) {
            return [];
        }

        return $callbackValidatedArray;
    }

    /**
     * Validate the incoming request using the given route callback and return the validated input data.
     */
    private function routeCallbackValidator(\Closure $routeCallback): array
    {
        [$closureArgs, $validatedArray] = $this->getClosureArgs($routeCallback);

        try {
            $result = $this->responseHandler->handleResponse($routeCallback(...$closureArgs));
        } catch (ValidationException | NotFoundException | BadRequestException $e) {
            $this->errorResponse([
                ['key' => 'match', 'code' => $e->getCode(), 'message' => $e->getMessage()]
            ]);
        }

        if ($result === true) {
            return Reception::$inputData;
        } elseif ($result === false) {
            $this->errorResponse([['key' => 'match']]);
        } elseif (is_array($result)) {
            return $result;
        }

        return $validatedArray;
    }
}
