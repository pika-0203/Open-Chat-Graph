<?php

namespace Shadow\Exceptions;

interface ExceptionHandlerInterface
{
    /**
     * Handles the specified \Throwable instance.
     *
     * @param \Throwable $e The \Throwable instance to handle.
     */
    public static function handleException(\Throwable $e);
}
