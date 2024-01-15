<?php

declare(strict_types=1);

namespace Shadow\Kernel;

use Shadow\Kernel\Dispatcher\ConstructorInjectionInterface;
use Shadow\Kernel\Dispatcher\ConstructorInjection;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class Application implements ApplicationInterface
{
    protected ConstructorInjectionInterface $ci;

    public function __construct(array $parameters = [])
    {
        $this->ci = new ConstructorInjection($parameters);
    }

    public function make(string $abstract): object
    {
        return $this->ci->constructorInjection($abstract);
    }

    public function singleton(string $className, null|\Closure|string $concrete = null): void
    {
        $this->ci->register($className, $concrete, true);
    }

    public function bind(string $className, null|\Closure|string $concrete = null): void
    {
        $this->ci->register($className, $concrete);
    }
}
