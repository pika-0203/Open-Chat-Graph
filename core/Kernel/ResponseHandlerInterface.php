<?php

namespace Shadow\Kernel;

interface ResponseHandlerInterface
{
    public function handleResponse(mixed $response): mixed;
}
