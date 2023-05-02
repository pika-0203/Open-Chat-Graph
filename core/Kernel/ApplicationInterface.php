<?php

namespace Shadow\Kernel;

interface ApplicationInterface
{
    public function make(string $abstract): object;
}
