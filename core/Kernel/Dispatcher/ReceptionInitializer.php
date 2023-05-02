<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\Reception;
use Shadow\Kernel\RouteClasses\RouteDTO;
use Shadow\Exceptions\ValidationException;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class ReceptionInitializer implements ReceptionInitializerInterface
{
    use TraitErrorResponse;

    private RouteDTO $routeDto;
    private RouteCallbackInvokerInterface $routeCallbackInvoker;

    public function __construct(?RouteCallbackInvokerInterface $routeCallbackInvoker = null)
    {
        $this->routeCallbackInvoker = $routeCallbackInvoker ?? new RouteCallbackInvoker;
    }

    public function init(RouteDTO $routeDto)
    {
        $this->routeDto = $routeDto;
        $this->routeFails = $routeDto->getFailsResponse();

        $this->getDomainAndHttpHost();
        Reception::$requestMethod =       $this->routeDto->requestMethod;
        Reception::$isJson =              $this->routeDto->isJson;

        Reception::$flashSession =        $this->getFlashSession();
        Reception::$inputData =           $this->parseRequestBody($this->routeDto->paramArray);
    }

    public static function getDomainAndHttpHost(): string
    {
        if (isset(Reception::$domain)) {
            return Reception::$domain;
        }

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        Reception::$domain = $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? '');

        return Reception::$domain;
    }

    /**
     * Get the flash session data if it exists and unset it from the session.
     *
     * @return array The flash session data
     */
    private function getFlashSession(): array
    {
        if (isset($_SESSION[FLASH_SESSION_KEY_NAME])) {
            $session = $_SESSION[FLASH_SESSION_KEY_NAME];
            unset($_SESSION[FLASH_SESSION_KEY_NAME]);
        } else {
            $session = [];
        }

        return $session;
    }

    /**
     * Parses the request body and returns the input data.
     *
     * @return array The input data passed with the incoming request.
     */
    private function parseRequestBody(): array
    {
        if (Reception::$requestMethod === 'GET') {
            return array_merge($_GET, $this->routeDto->paramArray);
        }

        if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            return array_merge($_GET, $this->parseJson(), $this->routeDto->paramArray);
        }

        return array_merge($_GET, $_POST, $_FILES, $this->routeDto->paramArray);
    }

    private function parseJson(): array
    {
        $requestBody = file_get_contents('php://input');
        if (!is_string($requestBody)) {
            return [];
        }

        $jsonArray = json_decode($requestBody, true);
        if (!is_array($jsonArray)) {
            return [];
        }

        return $jsonArray;
    }

    /**
     * Validate the incoming request using the built-in validators and the route callback validator, if available.
     * Store the validated input data in the static Reception::$inputData property.
     * 
     * @param array $inputArray
     * 
     * @return array Validated array
     * 
     * @throws InvalidArgumentException
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function callRequestValidator()
    {
        $builtinValidators = $this->routeDto->getValidater();
        $routeCallback = $this->routeDto->getRouteCallback();

        if ($builtinValidators === false) {
            if ($routeCallback === false) {
                Reception::$inputData = [];
                return;
            }

            $validatedArray = $this->routeCallbackInvoker->invoke($this->routeDto, $routeCallback);
        } else {
            $validatedArray = $this->validateUsingBuiltinValidators($builtinValidators);

            if ($routeCallback !== false) {
                $callbackValidatedArray = $this->routeCallbackInvoker->invoke($this->routeDto, $routeCallback);

                if (!empty($callbackValidatedArray)) {
                    $validatedArray = array_merge($validatedArray, $callbackValidatedArray);
                }
            }
        }

        Reception::$inputData = $validatedArray;
    }

    /**
     * Validate the incoming request using the built-in validators and return the validated input data.
     */
    private function validateUsingBuiltinValidators($validators)
    {
        $validatedArray = $this->callBuiltinValidator($validators);
        $routeCallback = $this->routeDto->getRouteCallback();
        if ($routeCallback === false) {
            return $validatedArray;
        }

        $callbackValidatedArray = $this->routeCallbackInvoker->invoke($this->routeDto, $routeCallback);
        if (empty($callbackValidatedArray)) {
            return $validatedArray;
        }

        return array_merge($validatedArray, $callbackValidatedArray);
    }

    private function callBuiltinValidator(array $validators): array
    {
        $validatedArray = [];
        $errors = [];

        foreach ($validators as $key => $validator) {
            $data = Reception::$inputData;
            $currentLevel = &$validatedArray;

            foreach (explode('.', $key) as $property) {
                $data = &$data[$property] ?? null;
                $currentLevel[$property] = null;
                $currentLevel = &$currentLevel[$property];
            }

            try {
                $validatedValue = $validator($data);
            } catch (ValidationException $e) {
                $errors[] = [
                    'key' => $key,
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ];
                $validatedValue = false;
            }

            $currentLevel = $validatedValue;
        }

        if (!empty($errors)) {
            $this->errorResponse($errors);
        }

        return $validatedArray;
    }
}
