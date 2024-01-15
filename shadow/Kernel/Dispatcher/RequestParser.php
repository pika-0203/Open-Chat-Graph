<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

/**
 * Parses incoming request URI.
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class RequestParser implements RequestParserInterface
{
    /**
     * Search for a matching path in an array of paths and extract parameter values from the request URI.
     *
     * @throws InvalidArgumentException If the request method is not available.
     * @throws LogicException           If there might be more than one parameter with the same name in the route definition for the same path.
     */
    public function parse(RouteDTO $routeDto, string $requestUri)
    {
        $requestUri = preg_replace('#^/|/$#', '', strtolower((string) strtok($requestUri, '?')));

        set_error_handler(fn ($errno, $errstr, $errfile, $errline) => throw new \LogicException(sprintf(
            'There might be more than one parameter with the same name in the route definition for the same path: [%d] %s in %s:%d',
            $errno,
            $errstr,
            $errfile,
            $errline
        )));

        foreach ($routeDto->routePathArray as $key => $route) {
            [$routePath, $routeRequestMethodArray] = $this->extractRoutePathAndMethods($route);

            $matchedParams = [];
            if (preg_match($this->getPattern($routePath), $requestUri, $matchedParams) === 1) {
                [$parsedPathArray, $paramArray] = $this->extractParams($routePath, $matchedParams);

                restore_error_handler();
                $routeDto->parsedPathArray = $parsedPathArray;
                $routeDto->paramArray = $paramArray;
                $routeDto->routeRequestMethodArray = $routeRequestMethodArray ?: false;
                $routeDto->setRouteArrayKey((int) $key);
                return $routeDto;
            }
        }

        restore_error_handler();

        if ($requestUri === '') {
            $routeDto->parsedPathArray = [''];
            $routeDto->setRouteArrayKey('root');
        } else {
            $routeDto->parsedPathArray = explode('/', $requestUri);
            $routeDto->setRouteArrayKey('dynamic');
        }

        $routeDto->paramArray = [];
        $routeDto->routeRequestMethodArray = false;
        return $routeDto;
    }

    /**
     * Extracts the route path and request methods from a given route string.
     *
     * @param string $route The route string.
     * @return array An array containing the route path and an array of request methods.
     * @throws InvalidArgumentException If an invalid request method is specified in the route string.
     */
    protected function extractRoutePathAndMethods(string $route): array
    {
        $splitRoute = explode('@', $route);
        $routePath = (string) array_shift($splitRoute);

        $routeRequestMethodArray = (array) $splitRoute;

        foreach ($routeRequestMethodArray as &$routeRequestMethod) {
            $routeRequestMethod = strtoupper($routeRequestMethod);

            if (!in_array($routeRequestMethod, RequestParserInterface::AVAILABLE_REQUEST_METHOD, true)) {
                throw new \InvalidArgumentException(
                    "Invalid request method: '{$routeRequestMethod}' in '{$route}': Define it to make it available."
                );
            }
        }

        return [$routePath, $routeRequestMethodArray];
    }

    /**
     * Get the regular expression pattern for a given path.
     *
     * @param string $routePath         The path to generate a pattern for.
     * @return string                   The regular expression pattern.
     * @throws InvalidArgumentException If the parametar name is invalid.
     */
    protected function getPattern(string $routePath): string
    {
        $pattern = str_replace('/', '\/', $routePath);

        $validateMatches = [];
        if (preg_match('/\{([^{}]+)\}/', $routePath, $validateMatches) !== 0) {
            if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $validateMatches[1]) === 0) {
                throw new \InvalidArgumentException(
                    "Invalid parametar name: '{$routePath}' : It must starts with letter or underscore, followed by any number of letters, numbers, or underscores."
                );
            }

            $pattern = preg_replace('<\{([^\/]+)\}>', '(?P<$1>[^\/]+)', $pattern);
        }

        return '<^' . $pattern . '$>';
    }

    /**
     * Extracts parameter values from the URI and return them along with the matched path.
     *
     * @param string $routePath
     * @param array $matchedParams  The results of preg_match
     * @return array                `[$parsedPathArray, $paramArray]`
     */
    protected function extractParams(string $routePath, array $matchedParams): array
    {
        $parsedPathArray = [];
        $paramArray = [];

        foreach (explode('/', $routePath) as $routePathPart) {
            if (preg_match('/^{.*}$/', $routePathPart)) {
                $key = substr($routePathPart, 1, -1);

                $paramArray[$key] = $matchedParams[$key];
                continue;
            }

            $parsedPathArray[] = $routePathPart;
        }

        $parsedPathArray = $parsedPathArray === [] ? [''] : $parsedPathArray;
        return [$parsedPathArray, $paramArray];
    }
}
