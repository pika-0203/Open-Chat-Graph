<?php

declare(strict_types=1);

namespace Shadow\Kernel\RouteClasses;

use Shadow\Kernel\Validator;
use Shared\Exceptions\ValidationException;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
abstract class AbstractRoute
{
    protected RouteDTO $routeDto;

    protected function createArrayKey(?string $requestMethod): array
    {
        $key = array_key_last($this->routeDto->routePathArray) ?? 'root';

        if ($requestMethod === null) {
            $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        } else {
            $requestMethod = strtoupper($requestMethod);
        }

        return [$key, $requestMethod];
    }

    protected function createValidationObject(...$argCache): object
    {
        return new class($argCache)
        {
            protected array $argCache;

            public function __construct($argCache)
            {
                $this->argCache = $argCache;
            }

            public function str(mixed $input)
            {
                if (is_array($this->argCache[1])) {
                    if (!array_reduce($this->argCache[1], fn ($acc, $curr) => $acc && is_string($curr), true)) {
                        throw new \InvalidArgumentException('The elements of $regex array must be strings only.', 1000);
                    }
                }
                
                return Validator::str($input, ...$this->argCache);
            }

            public function num(mixed $input)
            {
                return Validator::num($input, ...$this->argCache);
            }

            public function file(mixed $file)
            {
                if (!is_array($file) || empty($file['tmp_name'] ?? [])) {
                    if (!$this->argCache[0]) {
                        throw new ValidationException('File is empty.', 3000);
                    }

                    return null;
                }

                return Validator::uploadedFile($file, $this->argCache[1], $this->argCache[2]);
            }
        };
    }
}
