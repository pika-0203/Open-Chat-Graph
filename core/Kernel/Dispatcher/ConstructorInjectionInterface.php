<?php

namespace Shadow\Kernel\Dispatcher;

interface ConstructorInjectionInterface
{
    /**
     * Resolves a class constructor's dependencies recursively through constructor injection.
     *
     * @param string $className          The name of the class to resolve
     * @param array  &$resolvedInstances Instances that have already been resolved
     * 
     * @return object The resolved instance
     * 
     * @throws \ReflectionException
     */
    public function constructorInjection(string $className, array &$resolvedInstances = []): object;

    /**
     * Resolves an interface name to a concrete class name
     *
     * @param string $interfaceName The name of the interface to resolve
     * 
     * @return string               The name of the concrete class that implements the interface
     * 
     * @throws \LogicException
     */
    public function resolveInterfaceToClass(string $interfaceName): string;
}
