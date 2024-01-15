<?php

namespace Shadow\Kernel;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
interface ApplicationInterface
{
    /**
     * Create an instance of the given class.
     *
     * @param string $abstract The abstract class or interface name.
     * @return object The created object.
     */
    public function make(string $abstract): object;

    /**
     * Register a class in the container as a singleton.
     *
     * @param string $className The class name to register.
     * @param null|\Closure|string $concrete The concrete implementation or closure.
     */
    public function singleton(string $className, null|\Closure|string $concrete = null): void;

    /**
     * Register a class in the container.
     *
     * @param string $className The class name to register.
     * @param null|\Closure|string $concrete The concrete implementation or closure.
     */
    public function bind(string $className, null|\Closure|string $concrete = null): void;
}
