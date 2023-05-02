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

    public function __construct(?ConstructorInjectionInterface $ci = null)
    {
        $this->ci = $ci ?? new ConstructorInjection;
    }

    public function make(string $abstract): object
    {
        return $this->ci->constructorInjection($abstract);
    }
}
