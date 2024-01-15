<?php

declare(strict_types=1);

namespace Shadow\Kernel\RouteClasses;

use Shadow\Kernel\ResponseInterface;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class RouteSecond extends AbstractRoute implements RouteSecondInterface
{
    public function __construct(RouteDTO &$routeDto)
    {
        $this->routeDto = $routeDto;
    }

    public function match(\Closure|ResponseInterface $callback, ?string $requestMethod = null): static
    {
        [$key, $requestMethod] = $this->createArrayKey($requestMethod);
        $this->routeDto->routeCallbackArray[$key][$requestMethod] = $callback;

        return $this;
    }

    public function matchStr(
        string $parametaName,
        ?string $requestMethod = null,
        ?int $maxLen = null,
        string|array|null $regex = null,
        bool $emptyAble = false,
        ?string $default = ''
    ): static {
        [$key, $requestMethod] = $this->createArrayKey($requestMethod);

        $validator = $this->createValidationObject($maxLen, $regex, $emptyAble, \Shared\Exceptions\ValidationException::class, $default);

        $this->routeDto->routeValidatorArray[$key][$requestMethod][$parametaName] = $validator->str(...);

        return $this;
    }

    public function matchNum(
        string $parametaName,
        ?string $requestMethod = null,
        ?int $max = null,
        ?int $min = null,
        ?int $exactMatch = null,
        bool $emptyAble = false,
        ?int $default = 0
    ): static {
        [$key, $requestMethod] = $this->createArrayKey($requestMethod);

        $validator = $this->createValidationObject($max, $min, $exactMatch, $emptyAble, \Shared\Exceptions\ValidationException::class, $default);

        $this->routeDto->routeValidatorArray[$key][$requestMethod][$parametaName] = $validator->num(...);

        return $this;
    }

    public function matchFile(
        string $parametaName,
        array $allowedMimeTypes,
        int $maxFileSize = DEFAULT_MAX_FILE_SIZE,
        bool $emptyAble = false,
        ?string $requestMethod = null,
    ): static {
        [$key, $requestMethod] = $this->createArrayKey($requestMethod);
        $validator = $this->createValidationObject($emptyAble, $allowedMimeTypes, $maxFileSize);
        $this->routeDto->paramArray[$parametaName] = [];
        $this->routeDto->routeValidatorArray[$key][$requestMethod][$parametaName] = $validator->file(...);

        return $this;
    }

    public function middleware(array $name, ?string $requestMethod = null): static
    {
        [$key, $requestMethod] = $this->createArrayKey($requestMethod);

        if (
            isset($this->routeDto->routeMiddlewareArray[$key][$requestMethod])
            && is_array($this->routeDto->routeMiddlewareArray[$key][$requestMethod])
        ) {
            $this->routeDto->routeMiddlewareArray[$key][$requestMethod] = array_merge($this->routeDto->routeMiddlewareArray[$key][$requestMethod], $name);
        } else {
            $this->routeDto->routeMiddlewareArray[$key][$requestMethod] = $name;
        }

        return $this;
    }

    public function fails(ResponseInterface|false $callback, ?string $requestMethod = null): static
    {
        [$key, $requestMethod] = $this->createArrayKey($requestMethod);
        $this->routeDto->routeFailsArray[$key][$requestMethod] = $callback;

        return $this;
    }
}
