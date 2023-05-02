<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use App\Config\ConstructorInjectionClassMap;

class ConstructorInjection implements ConstructorInjectionInterface
{
    private array $classMap;
    private array $reflectionClasses;

    public function __construct(?array $classMap = null)
    {
        $this->classMap = $classMap ?? ConstructorInjectionClassMap::MAP;
    }

    public function constructorInjection(string $className, array &$resolvedInstances = []): object
    {
        if (isset($resolvedInstances[$className])) {
            return $resolvedInstances[$className];
        }

        if (!class_exists($className)) {
            $concreteName = $this->resolveInterfaceToClass($className);
        } else {
            $concreteName = $className;
        }

        $reflectionClass = $this->getReflectionClass($concreteName);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return new $concreteName();
        }

        $methodArgs = $this->getMethodArgs($constructor, $resolvedInstances);
        $resolvedInstances[$className] = $reflectionClass->newInstanceArgs($methodArgs);

        return $resolvedInstances[$className];
    }

    /**
     * Gets the resolved arguments for a given constructor.
     *
     * @param \ReflectionMethod $constructor The constructor to resolve dependencies for
     * @param array             &$resolvedInstances Instances that have already been resolved
     * 
     * @return array            An array of resolved dependencies for the constructor
     * 
     * @throws \ReflectionException
     */
    private function getMethodArgs(\ReflectionMethod $constructor, array &$resolvedInstances = []): array
    {
        $methodArgs = [];

        foreach ($constructor->getParameters() as $param) {
            $paramType = $param->getType();

            if ($paramType === null || $paramType->isBuiltin()) {
                continue;
            }

            $paramClassName = $paramType->getName();

            if (!class_exists($paramClassName)) {
                $paramClassName = $this->resolveInterfaceToClass($paramClassName);
            }

            if (isset($resolvedInstances[$paramClassName])) {
                $methodArgs[] = $resolvedInstances[$paramClassName];
                continue;
            }

            $methodArgs[] = $this->constructorInjection($paramClassName, $resolvedInstances);
        }

        return $methodArgs;
    }

    public function resolveInterfaceToClass(string $interfaceName): string
    {
        if (!isset($this->classMap[$interfaceName])) {
            throw new \LogicException("No implementation found for interface '{$interfaceName}'");
        }

        $className = $this->classMap[$interfaceName];

        if (!class_exists($className)) {
            throw new \LogicException("Class '{$className}' not found");
        }

        return $className;
    }

    /**
     * Gets a ReflectionClass instance for a given class name.
     *
     * @param string $className The name of the class to get a ReflectionClass instance for
     * 
     * @return \ReflectionClass A ReflectionClass instance for the given class
     * 
     * @throws \ReflectionException
     */
    private function getReflectionClass(string $className): \ReflectionClass
    {
        if (!isset($this->reflectionClasses[$className])) {
            $this->reflectionClasses[$className] = new \ReflectionClass($className);
        }

        return $this->reflectionClasses[$className];
    }
}
